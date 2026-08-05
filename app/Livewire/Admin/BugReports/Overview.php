<?php

namespace App\Livewire\Admin\BugReports;

use App\Models\BugReport;
use Livewire\Component;
use Livewire\WithPagination;

class Overview extends Component
{
    use WithPagination;

    public int $unreviewedPerPage = 10;
    public int $reviewedPerPage = 10;

    public string $unreviewedSearch = '';
    public string $reviewedSearch = '';

    public function undoReviewBug($reportId) {

    }

    public function render()
    {
        $unreviewedBugReports = BugReport::where('is_resolved', false)
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->unreviewedSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->unreviewedSearch . '%')
                      ->orWhere('page', 'like', '%' . $this->unreviewedSearch . '%');
            })
            ->paginate($this->unreviewedPerPage);

        $reviewedBugReports = BugReport::where('is_resolved', true)
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->reviewedSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->reviewedSearch . '%')
                      ->orWhere('page', 'like', '%' . $this->reviewedSearch . '%');
            })
            ->paginate($this->reviewedPerPage);

        return view('livewire.admin.bug-reports.overview', [
            'unreviewedBugReports' => $unreviewedBugReports,
            'reviewedBugReports' => $reviewedBugReports,
        ]);
    }
}
