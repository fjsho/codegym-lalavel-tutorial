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
        // ddd($request,
        //  $request->session(),
        //  $request->input('tmp_file_id'));

        $index = $request->input('tmp_file_id');
        if (session()->has('tmp_files.path.'.$index)) {
            //ファイルを削除
            $tmp_file_path = $request->session()->get('tmp_files.path.'.$index);
            Storage::disk('public')->delete('tmp/'.$tmp_file_path);
            //セッションを削除
            session()->forget('tmp_files.path.'.$index);
            session()->forget('tmp_files.name.'.$index);
            $flash = ['success' => __('Picture deleted successfully.')];
        } else {
            $flash = ['error' => __('Failed to delete the picture.')];
        }
        return redirect()->route('tasks.create', ['project' => $project->id])
            ->with($flash);
    }
}
