<?php

namespace App\View\Components\Investor;

use Illuminate\View\Component;

class Layout extends Component
{
    public $title;
    
    public function __construct($title = null)
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('components.investor.layout');
    }
}