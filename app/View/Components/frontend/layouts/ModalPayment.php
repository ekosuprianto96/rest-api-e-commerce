<?php

namespace App\View\Components\frontend\layouts;

use App\Models\PaymentMethod;
use Illuminate\View\Component;

class ModalPayment extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $isMobile = false;
    public function __construct($isMobile = false)
    {
        $this->isMobile = $isMobile;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $data['isMobile'] = $this->isMobile;
        $data['payments'] = PaymentMethod::where('status_payment', 1)->get();
    
        return view('components.frontend.layouts.modal-payment', compact('data'));
    }
}
