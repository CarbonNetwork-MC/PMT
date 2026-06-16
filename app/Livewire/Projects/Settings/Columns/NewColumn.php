<?php

namespace App\Livewire\Projects\Settings\Columns;

use App\Models\ColumnColor;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class NewColumn extends Component
{
    public $project;
    public $colors;

    public $name;
    public $colorId;
    public $color;
    public $position;
    public $columnType;

    public $minColumns = 1;
    public $maxColumns = 5;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->colors = ColumnColor::all();

        // Fill position with the next available position
        $this->position = $this->project->columns()->count() + 1;
    }

    public function updated($key, $value) {
        if ($key === 'colorId') {
            $this->color = ColumnColor::find($value);
        }
    }

    public function save() {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'colorId' => ['required', 'exists:column_colors,id'],
            'position' => ['required', 'integer', 'min:' . $this->minColumns, 'max:' . $this->maxColumns],
            'columnType' => ['required', 'in:todo,doing,done'],
        ]);

        DB::transaction(function () {
            $columns = $this->project->columns()
                ->orderBy('position')
                ->get();

            foreach ($columns as $index => $column) {
                $column->update(['position' => $index + 1]);
            }

            $count = $columns->count();

            if ($count >= $this->maxColumns) {
                redirect()
                    ->route('projects.settings.columns.render', ['uuid' => $this->project->uuid])
                    ->error(__('settings.toast.max_columns_reached'));
                return;
            }

            // Clamp position
            $newPosition = min($this->position, $count + 1);

            // Shift existing columns down
            $this->project->columns()
                ->where('position', '>=', $newPosition)
                ->increment('position');

            // Insert new column
            $this->project->columns()->create([
                'name' => $this->name,
                'color_id' => $this->colorId,
                'position' => $newPosition,
                'column_type' => $this->columnType,
            ]);
        });

        return redirect()
            ->route('projects.settings.columns.render', ['uuid' => $this->project->uuid])
            ->success(__('settings.toast.column_added', ['name' => $this->name]));
    }

    public function render()
    {
        return view('livewire.projects.settings.columns.new-column');
    }
}
