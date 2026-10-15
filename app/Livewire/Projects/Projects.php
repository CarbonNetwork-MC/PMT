<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class Projects extends Component
{
    public $user;
    public $projects = [];
    public $projectCount = 0;

    public function mount() {
        $this->user = auth()->user();
        $this->projects = Project::where('owner_uuid', $this->user->uuid)
            ->orWhereHas('members', function($query) {
                $query->where('user_uuid', $this->user->uuid);
            })
            ->with(['owner', 'members.user'])
            ->orderBy('created_at')
            ->get();
        $this->projectCount = $this->projects ? count($this->projects) : 0;
    }

    public function render()
    {
        return view('livewire.projects.projects');
    }
}
