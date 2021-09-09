<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectCreateTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function ログインしてプロジェクト作成を開くと表示される()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('projects.create'));
        $response->assertSuccessful()->assertViewIs('projects.create');
    }

    /**
     * @test
     */
    public function 未ログインでプロジェクト作成を開くとログインページにリダイレクトされる()
    {
        $response = $this->get(route('projects.create'));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function プロジェクトが作成できる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.index'), [
            'key' => $this->faker->regexify('[A-Z0-9-_]{1,255}'),
            'name' => $this->faker->words(rand(1, 5), true),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('projects.index'));
    }

    /**
     * @test
     */
    public function 未ログインで作成するとログインページにリダイレクトされる()
    {
        $response = $this->post(route('projects.index'), [
            'key' => $this->faker->regexify('[A-Z0-9-_]{1,255}'),
            'name' => $this->faker->words(rand(1, 5), true),
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function プロジェクトキーが空だとエラーになる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.index'), [
            'key' => '',
            'name' => $this->faker->words(rand(1, 5), true),
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     * @group プロジェクト作成
     */
    public function プロジェクトキーに日本語を入力するとエラーになる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.index'), [
            'key' => 'ほげ',
            'name' => $this->faker->words(rand(1, 5), true),
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function プロジェクトキーが255文字を超えるとエラーになる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.index'), [
            'key' => $this->faker->regexify('[A-Z0-9_-]{256}'),
            'name' => $this->faker->words(rand(1, 5), true),
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function プロジェクトキーが重複するとエラーになる()
    {
        $user = User::factory()->create();

        $key = $this->faker->regexify('[A-Z0-9_-]{1,255}');
        Project::factory()->create([
            'key' => $key,
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('projects.index'), [
            'key' => $key,
            'name' => $this->faker->words(rand(1, 5), true),
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 削除済みのプロジェクトキーだと重複エラーにならない()
    {
        $user = User::factory()->create();
        $key = $this->faker->regexify('[A-Z0-9_-]{1,255}');
        $project = Project::factory()->create([
            'key' => $key,
            'created_user_id' => $user->id,
        ]);
        $project->delete();

        $response = $this->actingAs($user)->post(route('projects.index'), [
            'key' => $key,
            'name' => $this->faker->words(rand(1, 5), true),
        ]);

        $response->assertSessionHasNoErrors();
    }

    /**
     * @test
     */
    public function プロジェクト名が空だとエラーになる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.index'), [
            'key' => $this->faker->regexify('[A-Z0-9_-]{1,255}'),
            'name' => '',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function プロジェクト名が255文字を超えるとエラーになる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.index'), [
            'key' => $this->faker->regexify('[A-Z0-9_-]{1,255}'),
            'name' => $this->faker->realText(256),
        ]);

        $response->assertSessionHasErrors();
    }
}
