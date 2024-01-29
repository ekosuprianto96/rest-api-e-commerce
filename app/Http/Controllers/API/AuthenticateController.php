<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\TrxIorPay;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Jobs\SendInvoiceToko;
use App\Jobs\SendNotificationEmail;
use App\Jobs\SendNotificationLogin;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\TransaksiKomisiReferal;
use App\Notifications\LoginNotification;
use Illuminate\Support\Facades\Validator;
use App\Notifications\RegisterNotification;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\API\Handle\ErrorController;
use App\Models\LogUser;
use App\Models\Pemberitahuan;

class AuthenticateController extends Controller
{

    public function notifikasi(Request $request)
    {
        try {
            $notifikasi = Notification::where([
                'to' => $request['uuid_user'],
                'status_read' => 0
            ])->get();

            $detail['pesan'] = count(collect($notifikasi)->where('type', 'pesan'));
            $detail['order'] = count(collect($notifikasi)->where('type', 'order'));

            return response()->json(
                [
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil get notifikasi',
                    'detail' => $detail
                ]
            );
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
    public function authenticate(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email|min:6|max:32',
            'password' => 'required|min:6|max:16'
        ]);

        try {

            $credentials = $request->only('email', 'password');
            if (!$token = auth()->guard('api')->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Email atau Password Anda salah'
                ], 401);
            }


            // if(Auth::attempt($validator)) {
            // }
            $user = User::where('uuid', auth()->guard('api')->user()->uuid)->first();
            Auth::login($user);

            if (isset($user) && isset($user->email_verified_at) && $user->status_banned > 0) {
                SendNotificationLogin::dispatch($user);
            }

            LogUser::create([
                'uuid_user' => $user->uuid,
                'tgl_login' => Carbon::now()->format('Y-m-d')
            ]);

            return response()->json([
                'status' => true,
                'error' => false,
                'detail'    => [
                    'user' => auth()->guard('api')->user(),
                    'toko' => auth()->guard('api')->user()->toko,
                    'cart' => auth()->guard('api')->user()->cart,
                    'pemberitahuan' => auth()->guard('api')->user()->pemberitahuan,
                    'wishlist' => auth()->guard('api')->user()->wishlist
                ],
                'token'   => $token
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => false,
                'error'    => true,
                'detail' => $err->getMessage() . '-' . $err->getLine(),
                'message' => 'Maaf!, Sepertinya Terjadi Kesalahan System, Silahkan Coaba Beberapa Menit Lagi.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            //remove token
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                session()->regenerate(true);
                //return response JSON
                LogUser::create([
                    'uuid_user' => auth()->guard('api')->user()->uuid,
                    'tgl_logout' => Carbon::now()->format('Y-m-d')
                ]);
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Logout Berhasil!',
                ], 200);
            }
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:6|max:12',
            'full_name' => 'required|string|min:3|max:50',
            'email' => 'required|email|min:6|max:32',
            'no_hape' => 'required|min:8|max:14',
            'alamat' => 'required|min:8|max:255',
            'tgl_lahir' => 'required'
        ]);

        try {

            $user = User::where('uuid', Auth::user()->uuid)->update($request->all());

            if ($user) {

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $fileName = Auth::user()->uuid . '.' . $file->getClientOriginalExtension();
                    $path = asset('assets/user/image/' . Auth::user()->uuid . '/') . $fileName;
                    $file->move($path, $fileName);
                    $user->update([
                        'image' => $path
                    ]);
                }
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Update Akun',
                    'detail' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'error' => true,
                    'message' => 'Gagal Update Akun, Silahkan Periksa Kembali Form Anda.',
                    'detail' => []
                ], 400);
            }
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function upload_image(Request $request)
    {
        $request->validate([
            'image' => 'image|mimes:jpg,png,svg,jpeg,webp|max:30000'
        ]);

        try {
            $user = User::where('uuid', Auth::user()->uuid)->first();

            if ($request->hasFile('image')) {
                if ($user->image) {
                    File::delete(public_path('assets/users/image/' . Auth::user()->username) . '/' . $user->image);
                }
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $image = asset('assets/users/image/' . Auth::user()->username . '/' . $fileName);
                $file->move(public_path('assets/users/image/' . Auth::user()->username), $fileName);
                $user->image = $image;
                $user->save();
                return response()->json([
                    'status' => true,
                    'error' => false,
                    'message' => 'Berhasil Update Foto.',
                    'image' => $image
                ], 200);
            }
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }

    public function komisi()
    {
        try {

            $trx_pay = TransaksiKomisiReferal::with(['trx_iorpay', 'produk'])->where([
                'kode_pay' => Auth::user()->iorPay->kode_pay,
            ])->get();

            $arrayKomisi = [];
            foreach ($trx_pay as $trx) {
                $listKomisi = [
                    'nama_toko' => $trx->produk->toko->nama_toko,
                    'image' => $trx->produk->image,
                    'nama_produk' => $trx->produk->nm_produk,
                    'detail_harga' => $trx->produk->getHargaDiskon(),
                    'total_komisi' => number_format($trx->trx_iorpay->total_fixed, 0),
                    'tanggal' => $trx->created_at->format('d M y')
                ];
                $arrayKomisi['data'][] = $listKomisi;
                // $trx->
            }

            // data chart
            $bulanSekarang = Carbon::now()->format('m');
            if ($bulanSekarang <= 6) {
                $month = ['Jan', 'Feb', 'Mar', 'Apr', 'Mey', 'Jun'];
                $data = [
                    [
                        'name' => 'Pendapatan',
                        'data' => [
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::JANUARY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::FEBRUARY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::MARCH)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::APRIL)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::MAY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::JUNE)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi')
                        ]
                    ]
                ];
            } else {
                $month = ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $data = [
                    [
                        'name' => 'Pendapatan',
                        'data' => [
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::JULY)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::AUGUST)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::SEPTEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::OCTOBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::NOVEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi'),
                            TransaksiKomisiReferal::whereMonth('created_at', Carbon::DECEMBER)->whereYear('created_at', Carbon::now()->format('Y'))->sum('total_komisi')
                        ]
                    ]
                ];
            }
            $arrayKomisi['charts'] = [
                'bulan' => $month,
                'data' => $data
            ];

            $arrayKomisi['total']['total_komisi'] = number_format($trx_pay->sum('total_komisi'), 0);
            $arrayKomisi['total']['total_produk'] = $trx_pay->count();
            return response()->json([
                'status' => true,
                'error' => false,
                'message' => 'Berhasil get komisi',
                'detail' => $arrayKomisi
            ], 200);
        } catch (\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
