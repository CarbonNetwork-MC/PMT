<?php

namespace App\Livewire\Projects\Dashboard;

use App\Helpers\CheckProjectPermissions;
use App\Models\Project;
use Livewire\Component;

class Dashboard extends Component
{
    public $project;
    public $sprints;
    public $shownSprints;
    public $users;

    public $memberCount;
    public $projectLogs;

    public int $limit = 10;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->sprints = $this->project->sprints()->where('status', 'active')->orderBy('end_date', 'desc')->get();

        // Get 4 sprints which are active, check their most recent logs and sort by the most recent log date
        $this->projectLogs = $this->project->logs()->whereIn('sprint_uuid', $this->sprints->pluck('uuid'))->get();
        $this->shownSprints = $this->sprints->sortByDesc(function ($sprint) {
            $sprintLogs = $this->projectLogs->where('sprint_uuid', $sprint->uuid);

            if ($sprintLogs->isEmpty()) return null; // No logs for this sprint
            
            return $sprintLogs->max('created_at'); // Get the most recent log date
        })
        ->take(4)
        ->values();
        
        $members = $this->project->members()
            ->with('user')
            ->get()
            ->sortBy([
                fn ($member) => $member->project_role_id === 2 ? 0 : 1,
                fn ($member) => strtolower($member->user->name),
            ])
            ->pluck('user');

        $this->users = collect([$this->project->owner])
            ->concat($members)
            ->unique('uuid')
            ->values();

        $this->memberCount = $this->project->members->count() + 1;
    }

    public function loadMoreLogs() {
        $this->limit += 10;
    }

    public function render()
    {
        $logs = $this->project->logs()
            ->whereIn('sprint_uuid', $this->sprints->pluck('uuid'))
            ->orderBy('created_at', 'desc')
            ->limit($this->limit + 1)
            ->get();

        $deleteSprintLogs = $this->project->logs()
            ->where('action', 'delete')
            ->where('table', 'sprints')
            ->orderBy('created_at', 'desc')
            ->get();

        $logs = $logs->merge($deleteSprintLogs)->sortByDesc('created_at');

        $hasMoreLogs = $logs->count() > $this->limit;

        return view('livewire.projects.dashboard.dashboard', [
            'logs' => $logs->take($this->limit),
            'hasMoreLogs' => $hasMoreLogs,
        ]);
    }
}
