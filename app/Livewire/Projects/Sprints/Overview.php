<?php

namespace App\Livewire\Projects\Sprints;

use App\Models\Log;
use App\Models\Sprint;
use Livewire\Component;
use Illuminate\Support\Str;

class Overview extends Component
{
    public $uuid;
    public $sprints;
    
    public $id;
    public $sprint;

    public $createSprintModal = false;
    public $editSprintModal = false;
    public $deleteSprintModal = false;

    public $name, $start_date, $end_date, $status;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->sprints = Sprint::where('project_id', $uuid)->with('cards')->orderBy('start_date')->get();
    }

    /**
     * Create a new sprint
     * 
     * @return void
     */
    public function createSprint() {
        // Validate the date
        $data = $this->validate([
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $data['uuid'] = Str::uuid()->toString();
        $data['project_id'] = $this->uuid;

        // Create the sprint
        $sprint = Sprint::create($data);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $sprint->uuid,
            'action' => 'create',
            'data' => json_encode($data),
            'table' => 'sprints',
            'description' => 'Created sprint <b>' . $data['name'] . '</b>',
        ]);

        // Close the modal
        $this->createSprintModal = false;
    }

    /**
     * Set the sprint id to edit
     * 
     * @param string $id
     * 
     * @return void
     */
    public function editSprintSetId($id) {
        $this->id = $id;
        $this->editSprintModal = true;
        $this->sprint = Sprint::find($id);

        $this->name = $this->sprint->name;
        $this->start_date = $this->sprint->start_date;
        $this->end_date = $this->sprint->end_date;
        $this->status = $this->sprint->status;
    }

    /**
     * Update the sprint
     * 
     * @return void
     */
    public function updateSprint() {
        // Validate the data
        $data = $this->validate([
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'status' => ['required', 'string'],
        ]);

        // Find the sprint
        $sprint = Sprint::find($this->id);

        // Update the sprint
        $sprint = $sprint->update($data);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $this->id,
            'action' => 'update',
            'data' => json_encode($data),
            'table' => 'sprints',
            'description' => 'Updated sprint <b>' . $data['name'] . '</b>',
        ]);

        // Close the modal
        $this->editSprintModal = false;
    }

    /**
     * Set the sprint id to delete
     * 
     * @param string $id
     * 
     * @return void
     */
    public function deleteSprint($id) {
        $this->id = $id;
        $this->deleteSprintModal = true;
    }

    /**
     * Destroy the sprint
     * 
     * @return void
     */
    public function destroySprint() {
        // Find the sprint
        $sprint = Sprint::find($this->id);

        // Delete the sprint
        $sprint->delete();

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $this->id,
            'action' => 'delete',
            'data' => json_encode($sprint),
            'table' => 'sprints',
            'description' => 'Deleted sprint <b>' . $sprint->name . '</b>',
        ]);

        // Close the modal
        $this->deleteSprintModal = false;
    }

    /**
     * Start the sprint
     * 
     * @param string $id
     * 
     * @return void
     */
    public function startSprint($id) {
        // Find the sprint
        $sprint = Sprint::find($id);

        // Update the sprint
        $sprint->update([
            'status' => 'active',
        ]);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $id,
            'action' => 'update',
            'data' => json_encode($sprint),
            'table' => 'sprints',
            'description' => 'Started sprint <b>' . $sprint->name . '</b>',
        ]);
    }

    /**
     * Complete the sprint
     * 
     * @param string $id
     * 
     * @return void
     */
    public function completeSprint($id) {
        // Find the sprint
        $sprint = Sprint::find($id);

        // Update the sprint
        $sprint->update([
            'status' => 'done',
        ]);

        // Update the sprints
        $this->sprints = Sprint::where('project_id', $this->uuid)->with('cards')->orderBy('start_date')->get();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $id,
            'action' => 'update',
            'data' => json_encode($sprint),
            'table' => 'sprints',
            'description' => 'Completed sprint <b>' . $sprint->name . '</b>',
        ]);
    }

    /**
     * Render the component
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $activeSprints = Sprint::where('project_id', $this->uuid)->where('status', 'active')->get();
        $doneSprints = Sprint::where('project_id', $this->uuid)->where('status', 'done')->get();

        return view('livewire.projects.sprints.overview', [
            'activeSprints' => $activeSprints,
            'doneSprints' => $doneSprints,
        ]);
    }
}
