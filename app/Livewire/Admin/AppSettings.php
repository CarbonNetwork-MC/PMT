<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;

class AppSettings extends Component
{
    public $settings;
    public $settingsValues = [];

    public function mount() {
        $this->settings = Setting::all();

        foreach ($this->settings as $setting) {
            $this->settingsValues[$setting->id] = $setting->value;
        }
    }

    public function saveSetting($settingId) {
        $setting = Setting::find($settingId);
        $setting->value = $this->settingsValues[$settingId];
        $setting->save();
    }

    /**
     * Render the component.
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.app-settings');
    }
}
