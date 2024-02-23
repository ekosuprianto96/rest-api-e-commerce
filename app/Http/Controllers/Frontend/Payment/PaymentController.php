<?php

namespace App\Http\Controllers\Frontend\Payment;

use App\Http\Controllers\API\Handle\ErrorController;
use App\Http\Controllers\Controller;
use App\View\Components\frontend\layouts\ModalPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function render(Request $request) {
        try {

            return (new ModalPayment($request->isMobile))->render();
        }catch(\Exception $err) {
            return ErrorController::getResponseError($err);
        }
    }
}
