<?php

namespace App\Livewire\Projects\Sprints;

use App\Models\Log;
use App\Models\Project;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Overview extends Component
{
    public $project;
    public $sprints;

    public $sprintCount;
    public $activeSprints;
    public $completedSprints;
    public $archivedSprints;
    public $statuses = ['planned', 'active', 'completed'];

    public $name;
    public $start_date;
    public $end_date;
    public $status;

    public $editingSprint;
    public $deletingSprint;

    public $showEditModal = false;
    public $showDeleteModal = false;

    public $showModal = true;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();

        $this->sprintCount = $this->sprints->count();
        $this->activeSprints = $this->sprints->where('status', 'active')->where('is_archived', false)->count();
        $this->completedSprints = $this->sprints->where('status', 'completed')->where('is_archived', false)->count();
        $this->archivedSprints = $this->project->sprints()->where('is_archived', true)->count();
    }

    private function updateCounts() {
        $this->sprintCount = $this->sprints->count();
        $this->activeSprints = $this->sprints->where('status', 'active')->where('is_archived', false)->count();
        $this->completedSprints = $this->sprints->where('status', 'completed')->where('is_archived', false)->count();
        $this->archivedSprints = $this->project->sprints()->where('is_archived', true)->count();
    }

    public function editSprint($uuid) {
        $this->editingSprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $this->name = $this->editingSprint->name;
        $this->start_date = $this->editingSprint->start_date?->format('Y-m-d');
        $this->end_date = $this->editingSprint->end_date?->format('Y-m-d');
        $this->status = $this->editingSprint->status;
        $this->showEditModal = true;
    }

    public function updateSprint() {
        $this->editingSprint->update([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ]);

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();
        $this->showEditModal = false;

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->editingSprint->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $this->editingSprint->name,
                'start_date' => $this->editingSprint->start_date,
                'end_date' => $this->editingSprint->end_date,
                'status' => $this->editingSprint->status,
            ]),
            'description' => __('logs.sprints.updated', ['sprint' => $this->editingSprint->name]),
        ]);

        Toaster::success(__('sprints.toast.sprint-updated'));
    }

    public function deleteSprint($uuid) {
        $this->deletingSprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $this->showDeleteModal = true;
    }

    public function destroySprint() {
        $this->deletingSprint->delete();

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();
        $this->showDeleteModal = false;

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->deletingSprint->uuid,
            'action' => 'delete',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $this->deletingSprint->name,
                'start_date' => $this->deletingSprint->start_date,
                'end_date' => $this->deletingSprint->end_date,
                'status' => $this->deletingSprint->status,
            ]),
            'description' => __('logs.sprints.deleted', ['sprint' => $this->deletingSprint->name]),
        ]);

        Toaster::success(__('sprints.toast.sprint-deleted'));
    }

    public function startSprint($uuid) {
        $sprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $sprint->update(['status' => 'active']);

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $sprint->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $sprint->name,
                'start_date' => $sprint->start_date,
                'end_date' => $sprint->end_date,
                'status' => $sprint->status,
            ]),
            'description' => __('logs.sprints.status_changed', ['sprint' => $sprint->name, 'status' => $sprint->status]),
        ]);

        Toaster::success(__('sprints.toast.start_sprint', ['name' => $sprint->name]));
    }

    public function completeSprint($uuid) {
        $sprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $sprint->update(['status' => 'completed']);

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $sprint->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $sprint->name,
                'start_date' => $sprint->start_date,
                'end_date' => $sprint->end_date,
                'status' => $sprint->status,
            ]),
            'description' => __('logs.sprints.status_changed', ['sprint' => $sprint->name, 'status' => $sprint->status]),
        ]);

        Toaster::success(__('sprints.toast.complete_sprint', ['name' => $sprint->name]));
    }

    public function archiveSprint($uuid) {
        $sprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $sprint->update(['is_archived' => true]);

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $sprint->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $sprint->name,
                'start_date' => $sprint->start_date,
                'end_date' => $sprint->end_date,
                'status' => $sprint->status,
            ]),
            'description' => __('logs.sprints.archived', ['sprint' => $sprint->name]),
        ]);

        Toaster::success(__('sprints.toast.archive_sprint', ['name' => $sprint->name]));
    }
    
    public function render()
    {
        return view('livewire.projects.sprints.overview');
    }
}
