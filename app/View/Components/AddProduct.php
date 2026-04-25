<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddProduct extends Component
{
    public string $url;
    public string $name;

    /**
     * Create a new component instance.
     */
    public function __construct(string $url, string $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.add-product');
    }
}