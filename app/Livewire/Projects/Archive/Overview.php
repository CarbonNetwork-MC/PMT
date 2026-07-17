<?php

namespace App\Livewire\Projects\Archive;

use App\Helpers\CheckProjectPermissions;
use App\Models\Log;
use App\Models\Project;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Overview extends Component
{
    public $project;
    public $sprints;

    public $isProjectAdminOrOwner = false;

    public $sprintToDelete = null;
    public $showDeleteModal = false;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->sprints = $this->project->sprints()->where('is_archived', true)->get();

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(auth()->user(), $this->project);
    }

    public function deleteSprint($uuid) {
        $this->sprintToDelete = $this->project->sprints()->where('uuid', $uuid)->firstOrFail();
        $this->showDeleteModal = true;
    }

    public function destroySprint() {
        $sprintName = $this->sprintToDelete->name;

        $this->sprintToDelete->delete();
        $this->sprints = $this->project->sprints()->where('is_archived', true)->get();
        $this->showDeleteModal = false;

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'action' => 'delete',
            'table' => 'sprints',
            'data' => json_encode([]),
            'description' => __('logs.sprints.deleted', [
                'sprint' => $sprintName,
            ]),
            'environment' => app()->environment(),
        ]);

        Toaster::success(__('archive.toasts.delete_sprint', ['name' => $sprintName]));
    }

    public function unarchiveSprint($uuid) {
        $sprint = $this->project->sprints()->where('uuid', $uuid)->firstOrFail();
        $sprint->update([
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
        ]);

        $this->sprints = $this->project->sprints()->where('is_archived', true)->get();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([]),
            'description' => __('logs.sprints.unarchived', [
                'sprint' => $sprint->name,
            ]),
            'environment' => app()->environment(),
        ]);

        Toaster::success(__('archive.toasts.unarchive_sprint', ['name' => $sprint->name]));
    }

    public function render()
    {
        return view('livewire.projects.archive.overview');
    }
}
