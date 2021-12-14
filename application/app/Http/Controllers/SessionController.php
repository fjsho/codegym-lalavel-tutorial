<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\TaskPictureStoreRequest;

class SessionController extends Controller
{
    /**
     * store the picture temporarily.
     *
     * @return \Illuminate\Http\Response
     */
    public function tmpStorePicture(TaskPictureStoreRequest $request, Project $project)
    {
        //投稿予定画像がある場合、セッション情報を一時的に保持する
        if($file = $request->file('file')){
            $client_original_name = $file->getClientOriginalName();
            $tmp_file_path = basename($file->store('public/tmp'));
            $request->session()->push('tmp_files.path', $tmp_file_path);
            $request->session()->push('tmp_files.name', $client_original_name);
            // ddd($file, $request->session());
        }

        return redirect()->route('tasks.create', ['project' => $project->id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskPicture  $taskPicture
     * @return \Illuminate\Http\Response
     */
    public function destroyTmpPicture(Project $project, Request $request)
    {
        ddd($request, $project, session(),session('tmp_files.path'));
        if (session()->has('tmp_files.path')) {
            // Storage::disk('public/tmp')->delete($tmp_stored_picture_path);
            // session()->forget('tmp_stored_picture_path',$tmp_stored_picture_path);
            $flash = ['success' => __('Picture deleted successfully.')];
        } else {
            $flash = ['error' => __('Failed to delete the picture.')];
        }

        return redirect()->route('tasks.create', ['project' => $project->id])
            ->with($flash);
    }
}
