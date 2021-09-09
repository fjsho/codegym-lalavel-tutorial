<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskResolution extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\TaskResolution::insert([
            ['name' => '対応済み', 'display_order' => 1],
            ['name' => '対応しない', 'display_order' => 2],
            ['name' => '無効', 'display_order' => 3],
            ['name' => '重複', 'display_order' => 4],
            ['name' => '再現しない', 'display_order' => 5],
        ]);
    }
}
