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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSprint() {
        $data = $this->validate([
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $data['id'] = Str::uuid()->toString();
        $data['project_id'] = $this->uuid;

        $sprint = Sprint::create($data);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'sprint_id' => $sprint->id,
            'action' => 'create',
            'data' => json_encode($data),
            'table' => 'sprints',
            'description' => 'Created sprint <b>' . $data['name'] . '</b>',
        ]);

        $this->createSprintModal = false;

        return redirect()->route('projects.sprints.render', $this->uuid);
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSprint() {
        $data = $this->validate([
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'status' => ['required', 'string'],
        ]);

        $sprint = Sprint::find($this->id);

        $sprint = $sprint->update($data);

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

        $this->editSprintModal = false;

        return redirect()->route('projects.sprints.render', $this->uuid);
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroySprint() {
        $sprint = Sprint::find($this->id);

        $sprint->delete();

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

        $this->deleteSprintModal = false;

        return redirect()->route('projects.sprints.render', $this->uuid);
    }

    /**
     * Start the sprint
     * 
     * @param string $id
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startSprint($id) {
        $sprint = Sprint::find($id);

        $sprint = $sprint->update([
            'status' => 'active',
        ]);

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

        return redirect()->route('projects.sprints.render', $this->uuid);
    }

    /**
     * Complete the sprint
     * 
     * @param string $id
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeSprint($id) {
        $sprint = Sprint::find($id);

        $sprint = $sprint->update([
            'status' => 'done',
        ]);

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

        return redirect()->route('projects.sprints.render', $this->uuid);
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
