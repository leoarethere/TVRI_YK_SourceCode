<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeroCarousel extends Component
{
    public array $slides;

    public function __construct($slides = [])
    {
        $this->slides = $slides;
    }

    public function render(): View|Closure|string
    {
        return view('components.hero-carousel');
    }
}
