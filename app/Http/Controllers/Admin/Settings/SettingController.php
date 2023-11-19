<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\Controller;
use App\Models\SettingGateway;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
{
    public function index() {
        $settings_web = SettingWebsite::first();
        $settings_gateway = SettingGateway::first();

        return view('admin.settings.index', compact('settings_web', 'settings_gateway'));
    }

    public function get_settings() {
        $settings_web = SettingWebsite::first();
        $settings_gateway = SettingGateway::first();

         $settings = array(
            'status_gateway' => $settings_gateway->status_gateway
         );

         return response()->json([
            'status' => true,
            'error' => false,
            'message' => 'get success',
            'detail' => $settings
         ], 200);
    }

    public function update(Request $request) {
        $request->validate([
            'app_name' => 'required',
            'lama_clearing_saldo' => 'required|numeric|max:30',
            'biaya_platform' => 'required|min:1|max:100',
            'server_key' => 'required',
            'client_key' => 'required'
        ]);
        try {

            $settings_web = SettingWebsite::first();
            $settings_web->app_name = $request->app_name;
            $settings_web->lama_clearing_saldo = $request->lama_clearing_saldo;
            $settings_web->biaya_platform = $request->biaya_platform;
            $settings_web->biaya_admin = $request->biaya_admin;
            $settings_web->save();

            $settings_gateway = SettingGateway::first();
            $settings_gateway->server_key = $request->server_key;
            $settings_gateway->client_key = $request->client_key;
            $settings_gateway->status_gateway = $request->status_gateway;
            $settings_gateway->save();

            Alert::success('Sukses!', 'Berhasil Update Settings.');

            return redirect()->back();
        }catch(\Exception $err) {
            return ErrorController::getError($err);
        }
    }
}
