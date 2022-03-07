<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Project;
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
        $assigners = User::all();
        $assigner_id = $request->input('assigner_id');

        $projects = Project::all();
        $project_id = $request->input('project_id');

        $tasks = Task::select(
            'tasks.*',
            'tasks.id as key,'
        )
            ->with('project');

        if ($request->has('project_id') && isset($project_id)) {
            $tasks->join('projects', 'tasks.project_id', 'projects.id');
            $tasks->where('project_id', '=', $project_id);
        }

        if ($request->has('assigner_id') && isset($assigner_id)) {
            $tasks->leftJoin('users as search_assigner', 'tasks.assigner_id', 'search_assigner.id');
            $tasks->where('tasks.assigner_id', '=', $assigner_id);
        }
        $tasks = $tasks->get();

        return view('dashboard', [
            'assigners' => $assigners,
            'assigner_id' => $assigner_id,
            'projects' => $projects,
            'searched_project_id' => $project_id,
            'tasks' => $tasks,
        ]);
    }
}
