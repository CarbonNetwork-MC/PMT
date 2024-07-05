<?php

namespace App\Livewire\Projects;

use Livewire\Component;

class Dashboard extends Component
{
    public $user;
    public $uuid;

    public function mount($uuid)
    {
        $this->user = auth()->user();
        $this->uuid = $uuid;

        // Set the selected project
        session()->put('selected_project', $this->uuid);
    }

    public function render()
    {
        return view('livewire.projects.dashboard');
    }
}
