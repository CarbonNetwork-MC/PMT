<?php

namespace App\Livewire\Components;

use App\Models\Sprint;
use Livewire\Component;

class Sidebar extends Component
{
    public $user;
    public $open = false;
    
    public $activeSprints = null;

    public $selectedProject;
    public $selectedSprint;

    public function mount() {
        $this->user = auth()->user();

        if (session()->has('selected_project')) {
            $this->selectedProject = session('selected_project');
        }

        $activeSprints = Sprint::where('project_id', $this->selectedProject)->where('status', 'active')->get();
        if ($activeSprints->count() > 0) {
            $this->activeSprints = $activeSprints;
        }
    }

    public function render()
    {
        return view('livewire.components.sidebar');
    }

    public function toggleSidebar() {
        $this->open = !$this->open;
    }
}
