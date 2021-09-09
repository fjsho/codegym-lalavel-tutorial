<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskDeleteTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function 課題が削除できる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', [
            'project' => $project->id,
            'task' => $task,
        ]));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('tasks.index', ['project' => $project->id]));
    }

    /**
     * @test
     */
    public function 未ログインで削除するとログインページにリダイレクトされる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->delete(route('tasks.destroy', [
            'project' => $project->id,
            'task' => $task,
        ]));

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function 削除済みの課題を削除するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);
        $task->delete();

        $response = $this->actingAs($user)->delete(route('tasks.destroy', [
            'project' => $project->id,
            'task' => $task,
        ]));

        $response->assertStatus(404);
    }
}
