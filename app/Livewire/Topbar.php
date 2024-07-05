<?php

namespace App\Livewire;

use Livewire\Component;

class Topbar extends Component
{
    public $user;

    public function mount() {
        $this->user = auth()->user();
    }

    public function render()
    {
        return view('livewire.topbar');
    }
}
