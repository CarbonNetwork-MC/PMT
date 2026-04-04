<?php

namespace App\Livewire\Projects\Settings\Columns;

use App\Models\ColumnColor;
use App\Models\Project;
use App\Models\ProjectColumn;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditColumn extends Component
{
    public $project;
    public $column;

    public $colors;

    public $name;
    public $colorId;
    public $color;
    public $position;

    public $minColumns = 1;
    public $maxColumns = 5;

    public function mount($uuid, $columnId) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->column = ProjectColumn::where('id', $columnId)->firstOrFail();

        $this->colors = ColumnColor::all();

        $this->name = $this->column->name;
        $this->colorId = $this->column->color_id;
        $this->color = $this->column->color;
        $this->position = $this->column->position;
    }

    public function updated($key, $value) {
        if ($key === 'colorId') {
            $this->color = ColumnColor::find($value);
        }
    }

    public function update() {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'colorId' => ['required', 'exists:column_colors,id'],
            'position' => ['required', 'integer', 'min:' . $this->minColumns, 'max:' . $this->maxColumns],
        ]);

        DB::transaction(function () {
            $columns = $this->project->columns()
                ->orderBy('position')
                ->get();

            foreach ($columns as $index => $col) {
                $col->update(['position' => $index + 1]);
            }

            $count = $columns->count();

            $newPosition = min($this->position, $count);
            $oldPosition = $this->column->position;

            if ($newPosition !== $oldPosition) {
                if ($newPosition < $oldPosition) {
                    $this->project->columns()
                        ->whereBetween('position', [$newPosition, $oldPosition - 1])
                        ->increment('position');
                } else {
                    $this->project->columns()
                        ->whereBetween('position', [$oldPosition + 1, $newPosition])
                        ->decrement('position');
                }

                $this->column->position = $newPosition;
            }

            $this->column->update([
                'name' => $this->name,
                'color_id' => $this->colorId,
                'position' => $this->column->position,
            ]);
        });

        return redirect()
            ->route('projects.settings.columns.render', ['uuid' => $this->project->uuid])
            ->success(__('settings.toast.column_updated', ['name' => $this->name]));
    }

    public function render()
    {
        return view('livewire.projects.settings.columns.edit-column');
    }
}
