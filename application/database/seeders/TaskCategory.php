<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\TaskCategory::insert([
            ['name' => 'カテゴリ１', 'display_order' => 1],
            ['name' => 'カテゴリ２', 'display_order' => 1],
            ['name' => 'カテゴリ３', 'display_order' => 1],
        ]);
    }
}
