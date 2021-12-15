<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\TaskPictureStoreRequest;

class TmpPictureController extends Controller
{
    /**
     * store the picture temporarily.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeTmpPicture(TaskPictureStoreRequest $request, Project $project)
    {
        //投稿予定画像がある場合、セッション情報を一時的に保持する
        if($file = $request->file('file')){
            //キー名としてランダムな名前を付与
            $tmp_file_name = Str::random(20);
            //storage/app/public/tmpにファイルを保存し、パスを取得
            $tmp_file_path = basename($file->store('public/tmp'));
            $request->session()->put('tmp_files.'.$tmp_file_name, $tmp_file_path);

            $flash = ['success' => __('Picture added successfully.')];
        } else {
            $flash = ['error' => __('Failed to add the picture.')];
        }

        return redirect()
            ->route('tasks.create', ['project' => $project->id])
            ->with($flash);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaskPicture  $taskPicture
     * @return \Illuminate\Http\Response
     */
    public function destroyTmpPicture(Project $project, Request $request)
    {
        $tmp_file_name = $request->input('tmp_file_name');
        if (session()->has('tmp_files.'.$tmp_file_name)) {
            //セッションからパスを取り出してファイルを削除
            $tmp_file_path = $request->session()->pull('tmp_files.'.$tmp_file_name);
            Storage::disk('public')->delete('tmp/'.$tmp_file_path);

            $flash = ['success' => __('Picture deleted successfully.')];
        } else {
            $flash = ['error' => __('Failed to delete the picture.')];
        }
        return redirect()
            ->route('tasks.create', ['project' => $project->id])
            ->with($flash);
    }
}
