<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskKind;
use App\Models\TaskResolution;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'project_id' => optional(Project::inRandomOrder()->first())->id,
            'name' => $this->faker->unique()->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            // 'detail' => $this->faker->realText(random_int(10, 1000)),
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
            'created_user_id' => optional(User::inRandomOrder()->first())->id,
            'updated_user_id' => optional(User::inRandomOrder()->first())->id,
            'assigner_id' => optional(User::inRandomOrder()->first())->id,
            'task_category_id' => optional(TaskCategory::inRandomOrder()->first())->id,
            'task_resolution_id' => optional(TaskResolution::inRandomOrder()->first())->id,
        ];
    }
}
