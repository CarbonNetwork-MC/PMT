<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColumnColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'name' => 'red',
            ],
            [
                'name' => 'rose',
            ],
            [
                'name' => 'orange',
            ],
            [
                'name' => 'amber',
            ],
            [
                'name' => 'yellow',
                'text_color' => 300,
                'background_color' => 400,
            ],
            [
                'name' => 'lime',
            ],
            [
                'name' => 'green',
            ],
            [
                'name' => 'emerald',
            ],
            [
                'name' => 'teal',
            ],
            [
                'name' => 'cyan',
            ],
            [
                'name' => 'sky',
            ],
            [
                'name' => 'blue',
            ],
            [
                'name' => 'indigo',
            ],
            [
                'name' => 'violet',
            ],
            [
                'name' => 'purple',
            ],
            [
                'name' => 'fuchsia',
            ],
            [
                'name' => 'pink',
            ],
            [
                'name' => 'stone',
            ],
            [
                'name' => 'zinc',
            ],
            [
                'name' => 'slate',
            ]
        ];

        foreach ($colors as $color) {
            DB::table('column_colors')->insert([
                'name' => $color['name'],
                'text_color' => $color['text_color'] ?? 500,
                'background_color' => $color['background_color'] ?? 500,
            ]);
        }
    }
}
