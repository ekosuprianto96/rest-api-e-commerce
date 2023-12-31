<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\Controller;
use App\Models\FormProduk;
use App\Models\ListFormProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    public function store(Request $request) {
    
        try {
            foreach($request['form'] as $key => $form) {
                $list_form = ListFormProduk::where([
                    'kode_produk' => $request['kode_produk'],
                    'label' => $key
                ])->first();

                $check_data_form = FormProduk::where([
                    'kode_produk' => $request['kode_produk'],
                    'uuid_user' => Auth::user()->uuid,
                    'id_form' => $list_form->id,
                ])->first();

                if($check_data_form) {
                    $check_data_form->delete();
                }

                $data_form = new FormProduk();
                $data_form->kode_produk = $request['kode_produk'];
                $data_form->uuid_user = Auth::user()->uuid;
                $data_form->id_form = $list_form->id;
                $data_form->value = $form;
                $data_form->save();

            }
            return response()->json([
                'status' => true
            ]);
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
