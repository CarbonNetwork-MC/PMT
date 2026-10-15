<?php

namespace App\Livewire\Projects\Settings;

use App\Models\Project;
use App\Models\ProjectColumn;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ProjectColumns extends Component
{
    public $project;
    public $projectColumns;

    public $minColumns = 1;
    public $maxColumns = 5;

    public $isProjectOwner;
    public $isProjectAdmin;
    public $isAppAdmin;

    public $removeColumnId;
    public $showRemoveColumnModal = false;

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
        $this->isAppAdmin = auth()->user()->can('manage-projects');
    }

    public function removeColumn($columnId) {
        if (!$this->isProjectOwner && !$this->isProjectAdmin && !$this->isAppAdmin) {
            Toaster::error(__('general.toasts.unauthorized'));
            return;
        }

        $this->removeColumnId = $columnId;
        $this->showRemoveColumnModal = true;
    }

    public function confirmRemoveColumn() {
        if (!$this->isProjectOwner && !$this->isProjectAdmin && !$this->isAppAdmin) {
            Toaster::error(__('general.toasts.unauthorized'));
            return;
        }

        DB::transaction(function () {
            $column = ProjectColumn::findOrFail($this->removeColumnId);

            // Determine fallback column (first available excluding current)
            $fallbackColumn = ProjectColumn::where('project_uuid', $this->project->uuid)
                ->where('id', '!=', $column->id)
                ->orderBy('position')
                ->first();

            // Move cards to fallback column if it exists
            if ($fallbackColumn) {
                $column->cards()->update([
                    'column_id' => $fallbackColumn->id,
                ]);
            }

            $column->delete();

            // Re-fetch and normalize positions
            $columns = ProjectColumn::where('project_uuid', $this->project->uuid)
                ->orderBy('position')
                ->get();

            foreach ($columns as $index => $col) {
                $col->update([
                    'position' => $index + 1,
                ]);
            }

            $this->projectColumns = $columns;
        });

        $this->showRemoveColumnModal = false;

        Toaster::success(__('settings.toast.column_removed'));
    }

    public function render()
    {
        return view('livewire.projects.settings.project-columns');
    }
}
