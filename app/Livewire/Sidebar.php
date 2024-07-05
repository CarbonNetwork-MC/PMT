<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $user;
    public $open = false;
    public $selectedProject;

    public function mount() {
        $this->user = auth()->user();

        if (session()->has('selected_project')) {
            $this->selectedProject = session('selected_project');
        }
    }

    public function render()
    {
        return view('livewire.sidebar');
    }

    public function toggleSidebar() {
        $this->open = !$this->open;
    }
}
