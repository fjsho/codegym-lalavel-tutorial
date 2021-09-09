<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\TaskCategory;
use App\Models\TaskKind;
use App\Models\TaskResolution;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskCreateTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function ログインしてタスク作成を開くと表示される()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.create', ['project' => $project->id]));
        $response->assertSuccessful()->assertViewIs('tasks.create');
    }

    /**
     * @test
     */
    public function 未ログインでタスク作成を開くとログインページにリダイレクトされる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->get(route('tasks.create', ['project' => $project->id]));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function タスクが作成できる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'detail' => $this->faker->realText(random_int(10, 1000)),
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
            'updated_user_id' => optional(User::inRandomOrder()->first())->id,
            'assigner_id' => optional(User::inRandomOrder()->first())->id,
            'task_category_id' => optional(TaskCategory::inRandomOrder()->first())->id,
            'task_resolution_id' => optional(TaskResolution::inRandomOrder()->first())->id,
            'due_date' => date('Y/m/d'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('tasks.index', ['project' => $project->id]));
    }

    /**
     * @test
     */
    public function 未ログインで作成するとログインページにリダイレクトされる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function 課題種別が空だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題名が空だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
        ]);

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

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->realText(256),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 詳細が65536文字を超えるとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'detail' => $this->faker->realText(65536),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題状況が空だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題種別が文字列だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => 'a',
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題状況が文字列だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => 'a',
            'updated_user_id' => optional(User::inRandomOrder()->first())->id,
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 担当者が文字列だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
            'assigner_id' => 'a',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題カテゴリーが文字列だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
            'task_category_id' => 'a',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 課題完了理由が文字列だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
            'task_resolution_id' => 'a',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function 期限日が文字列だとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tasks.index', ['project' => $project->id]), [
            'name' => $this->faker->words(1, true),
            'task_kind_id' => optional(TaskKind::inRandomOrder()->first())->id,
            'task_status_id' => optional(TaskStatus::inRandomOrder()->first())->id,
            'due_date' => 'a',
        ]);

        $response->assertSessionHasErrors();
    }
}
