<?php

namespace App\Livewire\Projects\Settings;

use App\Models\Log;
use Livewire\Component;
use App\Models\Project;

class Overall extends Component
{
    public $uuid;
    public $project;

    public $user;
    
    public $name, $description;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->project = Project::where('uuid', $uuid)->first();

        $this->user = auth()->user();

        $this->name = $this->project->name;
        $this->description = $this->project->description;
    }

    /**
     * Save the project settings
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save() {
        $this->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $this->project->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'action' => 'update',
            'data' => json_encode(['name' => $this->name, 'description' => $this->description]),
            'description' => 'Updated project settings',
        ]);

        return redirect()->route('projects.settings.overall.render', $this->uuid);
    }

    /**
     * Render the livewire component
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.projects.settings.overall');
    }
}
