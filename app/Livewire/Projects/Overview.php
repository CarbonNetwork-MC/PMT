<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Str;

class Overview extends Component
{
    public $user;
    public $projects;

    public function mount() {

        $this->user = auth()->user();
        $this->projects = Project::where('owner_id', $this->user->uuid)->with('sprints')->with('members')->get();
    }

    public function render()
    {
        return view('livewire.projects.overview');
    }
}
