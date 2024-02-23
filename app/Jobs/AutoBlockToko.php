<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\LogUser;
use App\Models\DetailToko;
use Illuminate\Support\Str;
use App\Models\Pemberitahuan;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AutoBlockToko implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Ambil Log user yang mempunyai toko
        $tglSekarang = Carbon::now()->addDay(-4)->format('Y-m-d');
        $log = LogUser::selectRaw('detail_toko.*')
                        ->join('users', 'users.uuid', 'log_users.uuid_user')
                        ->join('detail_toko', 'detail_toko.uuid_user', 'users.uuid')
                        ->whereRaw("log_users.tgl_login <= '".$tglSekarang."'")
                        ->get();
        
        // cek jika ada data toko
        if(@count($log) > 0) {
            foreach($log as $toko) {
                $toko = DetailToko::where('kode_toko', $toko->kode_toko)->first();
                if(isset($toko)) {
                    if($toko->status_toko == 0) {
                        continue;
                    }else {
                        $produkToko = $toko->produk;

                        if(count($produkToko) > 0) {
                            foreach($produkToko as $produk) {
                                $produk->update([
                                    'an' => 0
                                ]);
                            }
                        }
                        $toko->update([
                            'status_toko' => 3
                        ]);

                        // Kirim Notifikasi
                        Pemberitahuan::create([
                            'uuid' => Str::uuid(),
                            'title' => 'Toko Di Non Aktifkan Untuk Semantara',
                            'body' => nl2br('Hallo!, '.$toko->user->full_name.'.
                                Dengan berat hati kami informasikan bahwa akun toko anda 
                                kami non aktifkan untuk semntara.
                                
                                Kebijakan kami untuk semua <strong>Toko</strong> yang terdaftar haru tetap aktif,
                                apabila <strong>Toko</strong> tidak aktif selama 3 hari 
                                maka akun <strong>Toko</strong> akan kami
                                non aktifkan, untuk menjaga kenyaman pembeli.


                                Terimakasih atas perhatian anda.
                                Salam Dari Admin <strong>'.config('app.name').'</strong>.
                            '),
                            'uuid_user' => $toko->uuid_user,
                            'tanggal' => Carbon::now()->format('Y-m-d')
                        ]);
                        
                        LogUser::where([
                            'uuid_user' => $toko->uuid_user
                        ])->delete();
                    }
                }
            }
        }
    }
}
