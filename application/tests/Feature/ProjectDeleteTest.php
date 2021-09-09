<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectDeleteTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function プロジェクトが削除できる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('projects.destroy', [
            'project' => $project,
        ]));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('projects');
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

        $response = $this->delete(route('projects.destroy', [
            'project' => $project,
        ]));

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function 削除済みのプロジェクトを削除するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $project->delete();

        $response = $this->actingAs($user)->delete(route('projects.destroy', [
            'project' => $project,
        ]));

        $response->assertStatus(404);
    }
}
