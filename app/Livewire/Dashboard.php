<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $projectsCount = 0;
    public $uniqueUsersCount = 0;

    public $projects;

    public function mount() {
        $this->projectsCount = auth()->user()->projects()->count();
        $this->uniqueUsersCount = auth()->user()->projects()->with('members')->get()->pluck('members')->flatten()->unique('user_uuid')->count();

        // Get projects the user is a member of (or owns), sort them by their most recent log and limit to 4
        $this->projects = auth()->user()
            ->projects()
            ->with('latestLog.sprint')
            ->withMax('logs', 'created_at')
            ->orderByDesc('logs_max_created_at')
            ->limit(4)
            ->get();

        // $project->latestLog?->sprint
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
