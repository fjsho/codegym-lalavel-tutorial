<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use App\Http\Requests\TaskCommentStoreRequest;

class TaskCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \app\Http\Requests\TaskCommentStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskCommentStoreRequest $request, Project $project, Task $task)
    {
        $validated = $request->validated();

        if (TaskComment::create([
            'task_id' => $task->id,
            'comment' => $validated['comment'],
            'created_user_id' => $request->user()->id,
        ])) {
            $flash = ['success' => __('Comment created successfully.')];
        } else {
            $flash = ['error' => __('Failed to create the Comment.')];
        }

        return redirect()->route('tasks.edit', ['project' => $project->id, 'task' => $task->id])
            ->with($flash);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskComment  $task_comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task, TaskComment $task_comment)
    {
        if ($task_comment->delete()) {
            $flash = ['success' => __('Comment deleted successfully.')];
        } else {
            $flash = ['error' => __('Failed to delete the comment.')];
        }

        return redirect()
            ->route('tasks.edit', ['task' => $task->id])
            ->with($flash);
    }
}
