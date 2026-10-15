<?php

namespace App\Livewire\Projects\Settings;

use App\Helpers\CheckIfUserIsAdmin;
use App\Models\Log;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class General extends Component
{
    public $project;

    public $name;
    public $description;
    
    public $isProjectOwner;
    public $isProjectAdmin;
    public $isAppAdmin;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();

        $this->name = $this->project->name;
        $this->description = $this->project->description;

        $this->isProjectOwner = auth()->user()->uuid === $this->project->owner_uuid;
        $this->isProjectAdmin = $this->project->members()
            ->where('user_uuid', auth()->user()->uuid)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Admin');
            })
            ->exists();
        $this->isAppAdmin = auth()->user()->can('manage-projects');
    }

    public function save() {
        if (!$this->isProjectOwner && !$this->isProjectAdmin && !$this->isAppAdmin) {
            Toaster::error(__('general.toasts.unauthorized'));
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $this->project->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'action' => 'update',
            'table' => 'projects',
            'data' => json_encode([
                'name' => $this->name,
                'description' => $this->description,
            ]),
            'description' => __('logs.project.updated', ['project' => $this->project->name]),
            'environment' => config('app.env'),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        return redirect()->route('projects.settings.general.render', ['uuid' => $this->project->uuid])->success(__('settings.toast.general_updated'));
    }

    public function render()
    {
        return view('livewire.projects.settings.general');
    }
}
