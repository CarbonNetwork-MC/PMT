<?php

namespace App\Livewire\Projects\Sprints;

use App\Models\Project;
use Livewire\Component;

class Overview extends Component
{
    public $project;
    public $sprints;

    public $sprintCount;
    public $activeSprints;
    public $completedSprints;
    public $archivedSprints;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->sprints = $this->project->sprints()->orderBy('created_at')->get();

        $this->sprintCount = $this->sprints->count();
        $this->activeSprints = $this->sprints->where('status', 'active')->count();
        $this->completedSprints = $this->sprints->where('status', 'completed')->count();
        $this->archivedSprints = $this->sprints->where('is_archived', true)->count();
    }
    
    public function render()
    {
        return view('livewire.projects.sprints.overview');
    }
}
