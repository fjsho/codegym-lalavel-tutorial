<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TaskIndexTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     * @group 課題一覧
     */
    public function ログインして課題一覧を開くと表示される()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index', ['project' => $project->id]));
        $response->assertSuccessful()->assertViewIs('tasks.index');
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function 未ログインで課題一覧を開くとログインページにリダイレクトされる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->get(route('projects.index', ['project' => $project->id]));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function キーワードが255文字を超えるとエラーになる()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->call('GET', route('projects.index', ['project' => $project->id]), [
            'keyword' => $this->faker->regexify('[A-Z0-9_-]{256}'),
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function 課題一覧が表示される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $tasks = Task::factory(10)->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index', ['project' => $project->id]));

        $response->assertSuccessful();
        $response->assertSee(__('Task Kind'));
        $response->assertSee(__('Task Key'));
        $response->assertSee(__('Task Name'));
        $response->assertSee(__('Task Assigner'));
        $response->assertSee(__('Created At'));
        $response->assertSee(__('Due Date'));
        $response->assertSee(__('Updated At'));
        $response->assertSee(__('Created User'));

        foreach ($tasks as $task) {
            $response->assertSee($task->task_kind->name);
            $response->assertSee("$task->key", false);
            $response->assertSee(">$task->name</a>", false);
            $response->assertSee($task->assigner->name);
            $response->assertSee($task->created_at->format('Y/m/d'));
            if (isset($task->due_date)) {
                $response->assertSee($task->due_date->format('Y/m/d'));
            }

            $response->assertSee($task->updated_at->format('Y/m/d'));
            $response->assertSee($task->user->name);
        }
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function キーワードで検索ができる()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        // 検索して1件だけヒットするように多めにレコードを作成する
        $tasks = Task::factory(40)->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $task = $tasks->pop();
        $response = $this->actingAs($user)->call('GET', route('tasks.index', ['project' => $project->id]), [
            'keyword' => $task->name,
        ]);

        $response->assertSuccessful();
        $response->assertSee($task->task_kind->name);
        $response->assertSee("$task->key", false);
        $response->assertSee(">$task->name</a>", false);
        $response->assertSee($task->assigner->name);
        $response->assertSee($task->created_at->format('Y/m/d'));
        if (isset($task->due_date)) {
            $response->assertSee($task->due_date->format('Y/m/d'));
        }

        $response->assertSee($task->updated_at->format('Y/m/d'));
        $response->assertSee($task->user->name);

        foreach ($tasks as $item) {
            $response->assertDontSee($item->key);
            $response->assertDontSee(">$item->name</a>", false);
        }
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function 担当者で検索ができる()
    {
        DB::table('tasks')->delete();
        DB::table('projects')->delete();
        $user = User::factory()->create();
        $assigner = User::factory()->create();
        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        // 検索して1件だけヒットするように多めにレコードを作成する
        $tasks = Task::factory(40)->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
            'assigner_id' => $user->id,

        ]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
            'assigner_id' => $assigner->id,
        ]);

        $response = $this->actingAs($user)->call('GET', route('tasks.index', ['project' => $project->id]), [
            'assigner_id' => $assigner->id,
        ]);

        $response->assertSuccessful();
        $response->assertSee($task->task_kind->name);
        $response->assertSee($task->key);
        $response->assertSee(">$task->name</a>", false);
        $response->assertSee($task->assigner->name);
        $response->assertSee($task->created_at->format('Y/m/d'));
        if (isset($task->due_date)) {
            $response->assertSee($task->due_date->format('Y/m/d'));
        }

        $response->assertSee($task->updated_at->format('Y/m/d'));
        $response->assertSee($task->user->name);

        foreach ($tasks as $item) {
            $response->assertDontSee($item->key);
            $response->assertDontSee(">$item->name</a>", false);
        }
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function 課題が指定の件数を超えるとページネーションが表示される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        Task::factory(21)->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index', ['project' => $project->id]));
        $response->assertSuccessful()->assertViewIs('tasks.index');
        $response->assertDontSee('Go to page 1');
        $response->assertSee('Go to page 2');
        $response->assertDontSee('Go to page 3');
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function URLにページのパラメータが含まれている場合指定のページが表示される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        Task::factory(21)->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->call('GET', route('tasks.index', ['project' => $project->id]), [
            'page' => 2,
        ]);
        $response->assertSuccessful()->assertViewIs('tasks.index');
        $response->assertSee('Go to page 1');
        $response->assertDontSee('Go to page 2');
        $response->assertDontSee('Go to page 3');
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function URLに存在しないページのパラメータが含まれている場合一覧が表示されない()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        Task::factory(21)->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->call('GET', route('tasks.index', ['project' => $project->id]), [
            'page' => 3,
        ]);
        $response->assertSuccessful()->assertViewIs('tasks.index');
        $response->assertDontSee(__('Task Kind'));
        $response->assertDontSee(__('Task Name'));
        $response->assertDontSee(__('Created At'));
        $response->assertDontSee(__('Due Date'));
        $response->assertDontSee(__('Updated At'));
        $response->assertDontSee(__('Created User'));
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function URLにキーワードとページのパラメータが含まれている場合指定のページが表示される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        $name = str_replace(',', '', $this->faker->words(rand(1, 5), true));
        Task::factory(41)->create([
            'name' => "$name",
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->call('GET', route('tasks.index', ['project' => $project->id]), [
            'keyword' => $name,
            'page' => 2,
        ]);
        $response->assertSuccessful()->assertViewIs('tasks.index');
        $response->assertSee('Go to page 1');
        $response->assertDontSee('Go to page 2');
        $response->assertSee('Go to page 3');
        $response->assertDontSee('Go to page 4');
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function URLにキーワードのパラメータが含まれている場合ページネーションのURLにもキーワードが設定される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $name = str_replace(',', '', $this->faker->words(rand(1, 5), true));
        $url_keyword = rawurlencode($name);
        Task::factory(41)->create([
            'name' => "$name",
            'project_id' => $project->id,
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->call('GET', route('tasks.index', ['project' => $project->id]), [
            'keyword' => $name,
            'page' => 2,
        ]);
        $response->assertSuccessful()->assertViewIs('tasks.index');
        $response->assertSee("keyword=$url_keyword");
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function URLに担当者とページのパラメータが含まれている場合指定のページが表示される()
    {
        $user = User::factory()->create();
        $assigner = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);
        Task::factory(41)->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
            'assigner_id' => $assigner->id,
        ]);

        $response = $this->actingAs($user)->call('GET', route('tasks.index', ['project' => $project->id]), [
            'assigner_id' => $assigner->id,
            'page' => 2,
        ]);
        $response->assertSuccessful()->assertViewIs('tasks.index');
        $response->assertSee('Go to page 1');
        $response->assertDontSee('Go to page 2');
        $response->assertSee('Go to page 3');
        $response->assertDontSee('Go to page 4');
    }

    /**
     * @test
     * @group 課題一覧
     */
    public function URLに担当者のパラメータが含まれている場合ページネーションのURLにもキーワードが設定される()
    {
        $user = User::factory()->create();
        $assigner = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $project = Project::factory()->create([
            'created_user_id' => $user->id,
        ]);

        $url_keyword = $assigner->id;
        Task::factory(41)->create([
            'project_id' => $project->id,
            'created_user_id' => $user->id,
            'assigner_id' => $assigner->id,
        ]);

        $response = $this->actingAs($user)->call('GET', route('tasks.index', ['project' => $project->id]), [
            'assigner_id' => $assigner->id,
            'page' => 2,
        ]);
        $response->assertSuccessful()->assertViewIs('tasks.index');
        $response->assertSee("assigner_id=$url_keyword");
    }
}
