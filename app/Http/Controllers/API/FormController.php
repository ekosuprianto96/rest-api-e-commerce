<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\Controller;
use App\Models\FormProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    public function store(Request $request) {
    
        try {
            foreach($request['form'] as $key => $form) {
                $data_form = new FormProduk();
                $data_form->kode_produk = $request['kode_produk'];
                $form->uuid_user = Auth::user()->uuid;
                $form->value = $form;
                $form->save();

                return response()->json([
                    'status' => true
                ]);
            }
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
