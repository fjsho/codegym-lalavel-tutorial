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
        $projects = Project::all();
        $assigners = User::all();

        $assigner_id = $request->input('assigner_id');

        $tasks = Task::select('*');
        if ($request->has('assigner_id') && isset($assigner_id)) {
            $tasks->leftJoin('users as search_assigner', 'tasks.assigner_id', 'search_assigner.id');
            $tasks->where('tasks.assigner_id', '=', $assigner_id);
        }
        $tasks = $tasks->get();

        return view('dashboard', [
            'projects' => $projects,
            'tasks' => $tasks,
            'assigners' => $assigners,
            'assigner_id' => $assigner_id,
        ]);
    }
}
