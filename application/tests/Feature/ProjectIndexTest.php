<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProjectIndexTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function ログインしてプロジェクト一覧を開くと表示される()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('projects.index'));
        $response->assertSuccessful()->assertViewIs('projects.index');
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function 未ログインでプロジェクト一覧を開くとログインページにリダイレクトされる()
    {
        $response = $this->get(route('projects.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function キーワードが255文字を超えるとエラーになる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->call('GET', route('projects.index'), [
            'keyword' => $this->faker->regexify('[A-Z0-9_-]{256}'),
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function プロジェクト一覧が表示される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();
        $projects = Project::factory(10)->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertSuccessful();
        $response->assertSee(__('Project Key'));
        $response->assertSee(__('Project Name'));
        $response->assertSee(__('Created At'));
        $response->assertSee(__('Updated At'));
        $response->assertSee(__('Tasks'));
        $response->assertSee(__('Tasks.create'));

        foreach ($projects as $project) {
            $response->assertSee(">$project->key</a>", false);
            $response->assertSee(">$project->name</a>", false);
            $response->assertSee($project->created_at->format('Y/m/d'));
            $response->assertSee($project->updated_at->format('Y/m/d'));
        }
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function キーワードで検索ができる()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();
        // 検索して1件だけヒットするように多めにレコードを作成する
        $projects = Project::factory(40)->create([
            'created_user_id' => $user->id,
        ]);
        $project = $projects->pop();

        $response = $this->actingAs($user)->call('GET', route('projects.index'), [
            'keyword' => $project->name,
        ]);

        $response->assertSuccessful();
        $response->assertSee(">$project->key</a>", false);
        $response->assertSee(">$project->name</a>", false);
        $response->assertSee($project->created_at->format('Y/m/d'));
        $response->assertSee($project->updated_at->format('Y/m/d'));

        foreach ($projects as $item) {
            $response->assertDontSee(">$item->key</a>", false);
            $response->assertDontSee(">$item->name</a>", false);
        }
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function プロジェクトが指定の件数を超えるとページネーションが表示される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        Project::factory(21)->create([
            'created_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('projects.index'));
        $response->assertSuccessful()->assertViewIs('projects.index');
        $response->assertDontSee('Go to page 1');
        $response->assertSee('Go to page 2');
        $response->assertDontSee('Go to page 3');
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function URLにページのパラメータが含まれている場合指定のページが表示される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        Project::factory(21)->create([
            'created_user_id' => $user->id,
        ]);
        $response = $this->actingAs($user)->call('GET', route('projects.index'), [
            'page' => 2,
        ]);
        $response->assertSuccessful()->assertViewIs('projects.index');
        $response->assertSee('Go to page 1');
        $response->assertDontSee('Go to page 2');
        $response->assertDontSee('Go to page 3');
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function URLに存在しないページのパラメータが含まれている場合一覧が表示されない()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        Project::factory(21)->create([
            'created_user_id' => $user->id,
        ]);
        $response = $this->actingAs($user)->call('GET', route('projects.index'), [
            'page' => 3,
        ]);
        $response->assertSuccessful()->assertViewIs('projects.index');
        $response->assertDontSee(__('Project Key'));
        $response->assertDontSee(__('Project Name'));
        $response->assertDontSee(__('Created At'));
        $response->assertDontSee(__('Updated At'));
        $response->assertDontSee(__('Tasks'));
        $response->assertDontSee(__('Tasks.create'));
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function URLにキーワードとページのパラメータが含まれている場合指定のページが表示される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $name = str_replace(',', '', $this->faker->words(rand(1, 5), true));
        Project::factory(41)->create([
            'name' => "$name",
            'created_user_id' => $user->id,
        ]);
        $response = $this->actingAs($user)->call('GET', route('projects.index'), [
            'keyword' => $name,
            'page' => 2,
        ]);
        $response->assertSuccessful()->assertViewIs('projects.index');
        $response->assertSee('Go to page 1');
        $response->assertDontSee('Go to page 2');
        $response->assertSee('Go to page 3');
        $response->assertDontSee('Go to page 4');
    }

    /**
     * @test
     * @group プロジェクト一覧
     */
    public function URLにキーワードのパラメータが含まれている場合ページネーションのURLにもキーワードが設定される()
    {
        $user = User::factory()->create();
        DB::table('tasks')->delete();
        DB::table('projects')->delete();

        $name = str_replace(',', '', $this->faker->words(rand(1, 5), true));
        $url_keyword = rawurlencode($name);
        $project = Project::factory(41)->create([
            'name' => $name,
            'created_user_id' => $user->id,
        ]);
        $count = count($project);
        $response = $this->actingAs($user)->call('GET', route('projects.index'), [
            'keyword' => $name,
            'page' => 2,
        ]);
        $response->assertSuccessful()->assertViewIs('projects.index');
        $response->assertSee("keyword=$url_keyword");
    }
}
