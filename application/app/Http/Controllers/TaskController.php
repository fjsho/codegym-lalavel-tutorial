<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskKind;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Project $project)
    {
        $request->validate([
            'keyword' => 'max:255',
            'assigner_id' => 'nullable|integer',
        ]);

        $assigners = User::all();

        $assigner_id = $request->input('assigner_id');
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
            ->join('projects', 'tasks.project_id', 'projects.id')
            ->where('project_id', '=', $project->id);
        if ($request->has('keyword') && $keyword != '') {
            $tasks
                ->join('users as search_users', 'tasks.created_user_id', 'search_users.id')
                ->join('task_kinds as search_task_kinds', 'tasks.task_kind_id', 'search_task_kinds.id')
                ->leftJoin('users as search_assigner', 'tasks.assigner_id', 'search_assigner.id');
            $tasks
                ->where(function ($tasks) use ($keyword) {
                    $tasks
                        ->where('search_task_kinds.name', 'like', '%'.$keyword.'%')
                        ->orWhere('projects.key', 'like', '%'.$keyword.'%')
                        ->orWhere('tasks.name', 'like', '%'.$keyword.'%')
                        ->orWhere('search_assigner.name', 'like', '%'.$keyword.'%')
                        ->orWhere('search_users.name', 'like', '%'.$keyword.'%');
                });
        }
        if ($request->has('assigner_id') && isset($assigner_id)) {
            $tasks->where('tasks.assigner_id', '=', $assigner_id);
        }
        $tasks = $tasks
            ->sortable('name')
            ->paginate(20)
            ->appends(['keyword' => $keyword]);

        return view('tasks.index', compact('tasks'), [
            'project' => $project,
            'assigners' => $assigners,
            'assigner_id' => $assigner_id,
            'keyword' => $keyword,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Project $project)
    {
        $task_kinds = TaskKind::all();
        $task_statuses = TaskStatus::all();
        $task_categories = TaskCategory::all();
        $assigners = User::all();

        return view('tasks.create', [
            'project' => $project,
            'task_kinds' => $task_kinds,
            'task_statuses' => $task_statuses,
            'task_categories' => $task_categories,
            'assigners' => $assigners,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'task_kind_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'task_status_id' => 'required|integer',
            'assigner_id' => 'nullable|integer',
            'task_category_id' => 'nullable|integer',
            'task_resolution_id' => 'nullable|integer',
            'due_date' => 'nullable|date',
        ]);

        if (Task::create([
            'project_id' => $project->id,
            'task_kind_id' => $request->task_kind_id,
            'name' => $request->name,
            'task_status_id' => $request->task_status_id,
            'assigner_id' => $request->assigner_id,
            'task_category_id' => $request->task_category_id,
            'due_date' => $request->due_date,
            'created_user_id' => $request->user()->id,
        ])) {
            $flash = ['success' => __('Task created successfully.')];
        } else {
            $flash = ['error' => __('Failed to create the task.')];
        }

        return redirect()->route('tasks.index', ['project' => $project->id])
            ->with($flash);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project, Task $task)
    {
        $this->edit($project, $task);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project, Task $task)
    {
        $task_kinds = TaskKind::all();
        $task_statuses = TaskStatus::all();
        $task_categories = TaskCategory::all();
        $assigners = User::all();

        return view('tasks.edit', [
            'project' => $project,
            'task_kinds' => $task_kinds,
            'task_statuses' => $task_statuses,
            'task_categories' => $task_categories,
            'assigners' => $assigners,
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $request->validate([
            'task_kind_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'task_status_id' => 'required|integer',
            'assigner_id' => 'nullable|integer',
            'task_category_id' => 'nullable|integer',
            'task_resolution_id' => 'nullable|integer',
            'due_date' => 'nullable|date',
        ]);

        if ($task->update($request->all())) {
            $flash = ['success' => __('Task updated successfully.')];
        } else {
            $flash = ['error' => __('Failed to update the task.')];
        }

        return redirect()
            ->route('tasks.edit', ['project' => $project->id, 'task' => $task])
            ->with($flash);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project, Task $task)
    {
        if ($task->delete()) {
            $flash = ['success' => __('Task deleted successfully.')];
        } else {
            $flash = ['error' => __('Failed to delete the task.')];
        }

        return redirect()
            ->route('tasks.index', ['project' => $project->id])
            ->with($flash);
    }
}
