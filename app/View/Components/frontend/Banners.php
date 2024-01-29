<?php

namespace App\View\Components\frontend;

use App\Models\MsBanner;
use Illuminate\View\Component;

class Banners extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $banners;
    public function __construct(MsBanner $banners)
    {
        $this->banners = $banners;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.frontend.banners');
    }
}
