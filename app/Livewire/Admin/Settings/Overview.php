<?php

namespace App\Livewire\Admin\Settings;

use App\Models\AppSetting;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Overview extends Component
{
    public $sprintId;

    public function mount() {
        $this->sprintId = AppSetting::where('key', 'sprint_id')->first()->value ?? '';
    }

    public function saveSprintId() {
        $this->validate([
            'sprintId' => ['required', 'string', 'max:255']
        ]);

        AppSetting::updateOrCreate(
            ['key' => 'sprint_id'],
            ['value' => $this->sprintId]
        );

        Toaster::success(__('admin.toasts.settings.sprint_id_updated'));
    }

    public function render()
    {
        return view('livewire.admin.settings.overview');
    }
}
