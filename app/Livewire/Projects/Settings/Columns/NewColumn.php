<?php

namespace App\Livewire\Projects\Settings\Columns;

use App\Models\ColumnColor;
use App\Models\Project;
use Livewire\Component;

class NewColumn extends Component
{
    public $project;
    public $colors;

    public $name;
    public $colorId;
    public $color;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->colors = ColumnColor::all();
    }

    public function updated($key, $value) {
        if ($key === 'colorId') {
            $this->color = ColumnColor::find($value);
        }
    }

    public function save() {
        $this->validate([
            'name' => 'required|string|max:255',
            'colorId' => 'required|exists:column_colors,id',
        ]);

        $columns = $this->project->columns()
            ->orderBy('position')
            ->get();

        foreach ($columns as $index => $column) {
            $column->update(['position' => $index + 1]);
        }

        $count = $columns->count();
        if ($count >= 5) {
            return redirect()->route('projects.settings.columns.render', ['uuid' => $this->project->uuid])->error(__('settings.toast.max_columns_reached'));
        }

        $newPosition = $count + 1;

        $this->project->columns()->create([
            'name' => $this->name,
            'color_id' => $this->colorId,
            'position' => $newPosition,
        ]);

        return redirect()->route('projects.settings.columns.render', ['uuid' => $this->project->uuid])->success(__('settings.toast.column_added', ['name' => $this->name]));
    }

    public function render()
    {
        return view('livewire.projects.settings.columns.new-column');
    }
}
