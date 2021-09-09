<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\TaskStatus::insert([
            ['name' => '未対応', 'display_order' => 1],
            ['name' => '処理中', 'display_order' => 2],
            ['name' => '処理済み', 'display_order' => 3],
            ['name' => '完了', 'display_order' => 4],
        ]);
    }
}
