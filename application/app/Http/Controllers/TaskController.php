<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskKind;
use App\Models\TaskStatus;
use App\Models\TaskComment;
use App\Models\TaskPicture;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;

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
                        ->where('search_task_kinds.name', 'like', '%' . $keyword . '%')
                        ->orWhere('projects.key', 'like', '%' . $keyword . '%')
                        ->orWhere('tasks.name', 'like', '%' . $keyword . '%')
                        ->orWhere('search_assigner.name', 'like', '%' . $keyword . '%')
                        ->orWhere('search_users.name', 'like', '%' . $keyword . '%');
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
    public function create(Request $request, Project $project)
    {
        $task_kinds = TaskKind::all();
        $task_statuses = TaskStatus::all();
        $task_categories = TaskCategory::all();
        $assigners = User::all();

        // 遷移元がtasks.create以外ならtmp_filesのセッションを破棄する処理
        //（投稿画像の一時保存及び破棄時、store失敗時、更新ボタン押下時に一時保存画像を残すことを想定した）
        $referer_url = $request->header('referer');
        $tasks_create_url = route('tasks.create', ['project' => $project->id]); 
        if ($referer_url !== $tasks_create_url) {
            $request->session()->forget('tmp_files'); 
        }

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
     * @param  \app\Http\Requests\TaskStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskStoreRequest $request, Project $project)
    {
        $validated = $request->validated();
        $created_user_id = $request->user()->id;

        if ($task = Task::create([
            'project_id' => $project->id,
            'task_kind_id' => $validated['task_kind_id'],
            'name' => $validated['name'],
            'task_detail' => $validated['task_detail'],
            'task_status_id' => $validated['task_status_id'],
            'assigner_id' => $validated['assigner_id'],
            'task_category_id' => $validated['task_category_id'],
            'due_date' => $validated['due_date'],
            'created_user_id' => $created_user_id,
        ])) {
            $flash = ['success' => __('Task created successfully.')];
            //@CHECK：このif文はファンクションにして外に出した方がいいだろうか？
            if($request->session()->has('tmp_files')) {
                $tmp_file_names = array_keys(session('tmp_files'));
                foreach($tmp_file_names as $tmp_file_name){
                    //tmpディレクトリの対象画像をpublcディレクトリに移動させる
                    $tmp_file_path = TaskPicture::movePictureToPublicFromTmp($tmp_file_name);
                    //対象画像の情報をtask_picturesテーブルに保存する
                    $result[] = TaskPicture::storePicture($task->id, $tmp_file_path, $created_user_id);
                }
                if(in_array('error', $result, true)){
                    $flash = array_merge($flash,array('error' => __('Failed to upload the picture.')));
                } 
            }
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
        $task_comments = TaskComment::where('task_id', '=', $task->id)
            ->oldest()
            ->get();
        $task_pictures = TaskPicture::where('task_id', '=', $task->id)
            ->get();

        return view('tasks.edit', [
            'project' => $project,
            'task_kinds' => $task_kinds,
            'task_statuses' => $task_statuses,
            'task_categories' => $task_categories,
            'assigners' => $assigners,
            'task' => $task,
            'task_comments' => $task_comments,
            'task_pictures' => $task_pictures,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \app\Http\Requests\TaskUpdateRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(TaskUpdateRequest $request, Project $project, Task $task)
    {
        $validated = $request->validated();

        if ($task->update([
            'task_kind_id' => $validated['task_kind_id'],
            'name' => $validated['name'],
            'task_detail' => $validated['task_detail'],
            'task_status_id' => $validated['task_status_id'],
            'assigner_id' => $validated['assigner_id'],
            'task_category_id' => $validated['task_category_id'],
            'due_date' => $validated['due_date'],
            'updated_user_id' => $request->user()->id,
        ])) {
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
