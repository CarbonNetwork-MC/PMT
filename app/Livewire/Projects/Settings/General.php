<?php

namespace App\Livewire\Projects\Settings;

use App\Models\Project;
use Livewire\Component;

class General extends Component
{
    public $project;

    public $name;
    public $description;
    
    public $isProjectOwnerOrAdmin;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();

        $this->name = $this->project->name;
        $this->description = $this->project->description;

        $this->isProjectOwnerOrAdmin =
            auth()->user()->uuid === $this->project->owner_uuid
            || $this->project->members()
                ->where('user_uuid', auth()->user()->uuid)
                ->whereHas('role', function ($q) {
                    $q->where('slug', 'admin');
                })
                ->exists();
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

        return redirect()->route('projects.settings.general.render', ['uuid' => $this->project->uuid])->success(__('settings.toast.general_updated'));
    }

    public function render()
    {
        return view('livewire.projects.settings.general');
    }
}
