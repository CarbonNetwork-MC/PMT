<?php

namespace App\Livewire\Projects\Settings;

use App\Models\Project;
use App\Models\ProjectColumn;
use Livewire\Component;

class ProjectColumns extends Component
{
    public $project;
    public $projectColumns;

    public $minColumns = 1;
    public $maxColumns = 5;

    public $isProjectOwner;
    public $isProjectAdmin;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->projectColumns = ProjectColumn::where('project_uuid', $this->project->uuid)->with(['color'])->orderBy('position')->get();

        $this->isProjectOwner = auth()->user()->uuid === $this->project->owner_uuid;
        $this->isProjectAdmin = $this->project->members()
            ->where('user_uuid', auth()->user()->uuid)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Admin');
            })
            ->exists();
    }

    public function render()
    {
        return view('livewire.projects.settings.project-columns');
    }
}
