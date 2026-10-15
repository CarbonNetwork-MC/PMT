<?php

namespace App\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tabs extends Component
{
    public array $tabs;
    public string $active;

    public function __construct(array $tabs, string $active)
    {
        $this->tabs = $tabs;
        $this->active = $active;
    }

    public function render()
    {
        return view('components.navigation.tabs');
    }
}
