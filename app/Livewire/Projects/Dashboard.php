<?php

namespace App\Livewire\Projects;

use Livewire\Component;

class Dashboard extends Component
{
    public $uuid;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
    }

    public function render()
    {
        return view('livewire.projects.dashboard');
    }
}
