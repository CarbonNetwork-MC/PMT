<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class Projects extends Component
{
    public $user;
    public $projects;

    public function mount() {
        $this->user = auth()->user();
        $this->projects = Project::where('owner_uuid', $this->user->uuid)
            ->orWhereHas('members', function($query) {
                $query->where('user_uuid', $this->user->uuid);
            })
            ->with(['owner', 'members.user'])
            ->get();
    }

    public function render()
    {
        return view('livewire.projects.projects');
    }
}
