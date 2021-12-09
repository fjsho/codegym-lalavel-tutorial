<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
        //投稿予定画像がある場合、画像をsessionストレージに保存しセッション情報を一時的に保持する
        if($tmp_store_picture = $request->file('file')->store('session')){
            $request->session()->push('tmp_store_picture', $tmp_store_picture);
        };
        // ddd($request, $request->file('file'), $request->session());

        return redirect()->route('tasks.create', ['project' => $project->id]);

    }

}
