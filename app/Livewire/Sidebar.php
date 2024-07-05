<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $user;
    public $open = false;

    public function mount() {
        $this->user = auth()->user();
    }

    public function render()
    {
        return view('livewire.sidebar');
    }

    public function toggleSidebar() {
        $this->open = !$this->open;
    }
}
