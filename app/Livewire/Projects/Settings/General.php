<?php

namespace App\Livewire\Projects\Settings;

use App\Models\Log;
use App\Models\Project;
use Livewire\Component;

class General extends Component
{
    public $project;

    public $name;
    public $description;
    
    public $isProjectOwner;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();

        $this->name = $this->project->name;
        $this->description = $this->project->description;

        $this->isProjectOwner = auth()->user()->uuid === $this->project->owner_uuid;
    }

    public function save() {
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
        ]);

        return redirect()->route('projects.settings.general.render', ['uuid' => $this->project->uuid])->success(__('settings.toast.general_updated'));
    }

    public function render()
    {
        return view('livewire.projects.settings.general');
    }
}
