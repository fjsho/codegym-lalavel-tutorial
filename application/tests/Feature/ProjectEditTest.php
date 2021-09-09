<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectEditTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function 未ログインでプロジェクト作成を開くとログインページにリダイレクトされる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $response = $this->get(route('projects.edit', ['project' => $project->id]));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function プロジェクトが更新できる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $params = [
            'key' => $this->faker->regexify('[A-Z0-9-_]{1,255}'),
            'name' => $this->faker->words(rand(1, 5), true),
        ];
        $response = $this->actingAs($user)
        ->put(route('projects.update', ['project' => $project->id]), $params);

        $assert_project = Project::find($project->id);
        $this->assertEquals($params['key'], $assert_project->key);
        $this->assertEquals($params['name'], $assert_project->name);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('projects.edit', ['project' => $project->id]));
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

        $params = [
            'key' => $this->faker->regexify('[A-Z0-9-_]{1,255}'),
            'name' => $this->faker->words(rand(1, 5), true),
        ];
        $response = $this
        ->put(route('projects.update', ['project' => $project->id]), $params);

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function プロジェクトキーに日本語を入力するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $params = [
            'key' => 'ほげ',
            'name' => $this->faker->words(rand(1, 5), true),
        ];
        $response = $this->actingAs($user)
        ->put(route('projects.update', ['project' => $project->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function プロジェクトキーが255文字を超えるとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $params = [
            'key' => $this->faker->regexify('[A-Z0-9-_]{256}'),
            'name' => $this->faker->words(rand(1, 5), true),
        ];
        $response = $this->actingAs($user)
        ->put(route('projects.update', ['project' => $project->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function プロジェクトキーが重複するとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $params = [
            'key' => $this->faker->regexify('[A-Z0-9-_]{1,255}'),
            'name' => $this->faker->words(rand(1, 5), true),
        ];
        // 重複元のプロジェクトを作成する
        Project::factory()->create([
            'key' => $params['key'],
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
        ->put(route('projects.update', ['project' => $project->id]), $params);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 削除済みのプロジェクトキーだと重複エラーにならない()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $params = [
            'key' => $this->faker->regexify('[A-Z0-9-_]{1,255}'),
            'name' => $this->faker->words(rand(1, 5), true),
        ];
        // 重複元のプロジェクトを作成する
        $redundant_project = Project::factory()->create([
            'key' => $params['key'],
            'created_user_id' => $user->id,
        ]);
        $redundant_project->delete();

        $response = $this->actingAs($user)
        ->put(route('projects.update', ['project' => $project->id]), $params);

        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     */
    public function プロジェクト名が255文字を超えるとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $params = [
            'key' => $this->faker->regexify('[A-Z0-9_-]{1,255}'),
            'name' => $this->faker->realText(256),
        ];
        $response = $this->actingAs($user)
        ->put(route('projects.update', ['project' => $project->id]), $params);

        $response->assertSessionHasErrors();
    }
}
