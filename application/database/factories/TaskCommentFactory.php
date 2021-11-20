<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskComment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'task_id' => optional(Task::inRandomOrder()->first())->id,
            'comment' => $this->faker->realText(random_int(10, 1000)),
            'created_user_id' => optional(User::inRandomOrder()->first())->id,
        ];
    }
}
