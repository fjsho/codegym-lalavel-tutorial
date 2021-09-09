<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskKind;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskEditTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function 未ログインで課題作成を開くとログインページにリダイレクトされる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->get(route('tasks.edit', ['project' => $project->id, 'task' => $task->id]));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function 課題が更新できる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => TaskKind::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 5), true),
            'task_status_id' => TaskStatus::inRandomOrder()->first()->id,
        ];
        $response = $this->actingAs($user)
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $assert_task = Task::find($task->id);
        $this->assertEquals($params['task_kind_id'], $assert_task->task_kind_id);
        $this->assertEquals($params['name'], $assert_task->name);
        $this->assertEquals($params['task_status_id'], $assert_task->task_status_id);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('tasks.edit', ['project' => $project->id, 'task' => $task->id]));
    }

    /**
     * @test
     */
    public function 未ログインで更新するとログインページにリダイレクトされる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => TaskKind::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 5), true),
            'task_status_id' => TaskStatus::inRandomOrder()->first()->id,
        ];

        $response = $this
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function 課題種別IDに日本語を入力するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => 'ほげ',
            'name' => $this->faker->words(rand(1, 5), true),
            'task_status_id' => TaskStatus::inRandomOrder()->first()->id,
        ];

        $response = $this->actingAs($user)
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題状態IDに日本語を入力するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => TaskKind::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 5), true),
            'task_status_id' => 'ほげ',
        ];

        $response = $this->actingAs($user)
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 担当者IDに日本語を入力するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => TaskKind::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 5), true),
            'task_status_id' => TaskStatus::inRandomOrder()->first()->id,
            'assigner_id' => 'ほげ',
        ];

        $response = $this->actingAs($user)
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題カテゴリーIDに日本語を入力するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => TaskKind::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 5), true),
            'task_status_id' => TaskStatus::inRandomOrder()->first()->id,
            'task_category_id' => 'ほげ',
        ];

        $response = $this->actingAs($user)
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題完了理由IDに日本語を入力するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => TaskKind::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 5), true),
            'task_status_id' => TaskStatus::inRandomOrder()->first()->id,
            'task_resolution_id' => 'ほげ',
        ];

        $response = $this->actingAs($user)
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題名が255文字を超えるとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => TaskKind::inRandomOrder()->first()->id,
            'name' => $this->faker->regexify('[A-Z0-9_-]{256}'),
            'task_status_id' => TaskStatus::inRandomOrder()->first()->id,
        ];
        $response = $this->actingAs($user)
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 詳細が65535文字を超えるとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $params = [
            'task_kind_id' => TaskKind::inRandomOrder()->first()->id,
            'name' => $this->faker->words(rand(1, 5), true),
            'detail' => $this->faker->regexify('[A-Z0-9_-]{65536}'),
            'task_status_id' => TaskStatus::inRandomOrder()->first()->id,
        ];
        $response = $this->actingAs($user)
            ->put(route('tasks.update', ['project' => $project->id, 'task' => $task->id]), $params);

        $response->assertSessionHasErrors();
    }
}
