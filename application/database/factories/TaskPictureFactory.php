<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskPicture;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskPictureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskPicture::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $storage_dir_path = './storage/app/public';
        $picture = $this->faker->image($storage_dir_path, 300, 300, 'city');
        $file_path = str_replace($storage_dir_path, '', $picture);

        return [
            'task_id' => optional(Task::inRandomOrder()->first())->id,
            'file_path' => $file_path,
            'created_user_id' => optional(User::inRandomOrder()->first())->id,
        ];
    }
}
