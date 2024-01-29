<?php

namespace App\View\Components\frontend\produk;

use App\Models\Produk;
use Illuminate\View\Component;

class cardProduk extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $produk;
    public function __construct($produk)
    {
        $this->produk = $produk;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.frontend.produk.card-produk');
    }
}
