<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'keyword' => 'max:255',
        ]);

        $keyword = $request->input('keyword');
        $tasks = Task::select(
            'tasks.*',
            'tasks.id as key,'
        )
            ->with('task_kind')
            ->with('task_status')
            ->with('assigner')
            ->with('user')
            ->with('project')
            //別名をつけておかないと@sortablelinkを押したときにエラーになるので注意（SQL文でテーブル名が競合する）
            ->join('projects as search_projects', 'tasks.project_id', 'search_projects.id')
            ->where('assigner_id', '=', $request->user()->id);
        if ($request->has('keyword') && $keyword != '') {
            $tasks
                ->join('users as search_users', 'tasks.created_user_id', 'search_users.id')
                ->join('task_kinds as search_task_kinds', 'tasks.task_kind_id', 'search_task_kinds.id')
                ->join('task_statuses as search_task_statuses', 'tasks.task_status_id', 'search_task_statuses.id');
            $tasks
                ->where(function ($tasks) use ($keyword) {
                    $tasks
                        ->where('search_projects.name', 'like', '%' . $keyword . '%')
                        ->orWhere('search_projects.key', 'like', '%' . $keyword . '%')
                        ->orWhere('search_task_kinds.name', 'like', '%' . $keyword . '%')
                        ->orWhere('tasks.name', 'like', '%' . $keyword . '%')
                        ->orWhere('search_task_statuses.name', 'like', '%' . $keyword . '%');
                });
        }
        $tasks = $tasks
            ->sortable('name')
            ->paginate(20)
            ->appends(['keyword' => $keyword]);

        return view('dashboard', compact('tasks'), [
            'keyword' => $keyword,
        ]);
    }
}
