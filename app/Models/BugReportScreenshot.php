<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugReportScreenshot extends Model
{
    protected $fillable = [
        'bug_report_id',
        'screenshot_path',
    ];

    public function bugReport(): BelongsTo {
        return $this->belongsTo(BugReport::class);
    }
}
