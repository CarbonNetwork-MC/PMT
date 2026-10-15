<?php

namespace App\Livewire\Admin\BugReports;

use App\Models\BugReport;
use Livewire\Component;

class ViewReport extends Component
{
    public BugReport $report;

    public function mount($reportId) {
        $this->report = BugReport::where('id', $reportId)->with(['screenshots'])->firstOrFail();
    }

    public function render()
    {
        return view('livewire.admin.bug-reports.view-report');
    }
}
