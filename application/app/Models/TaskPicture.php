<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TaskPicture extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'file_path',
        'created_user_id',
    ];

    /**
     * ユーザーのフルネーム取得.
     */
    public function getKeyAttribute()
    {
        return "{$this->task->key}-{$this->id}";
    }

    /**
     * 画像を所有しているタスクを取得.
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * 画像を所有しているユーザーを取得.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * 画像をtmpディレクトリからpublicディレクトリに移動させる
     */
    public static function movePictureToPublicFromTmp($file_name)
    {
        $file_path = session()->pull('tmp_files.'.$file_name);
        Storage::move('public/tmp/'.$file_path,'public/'.$file_path);

        return $file_path;
    }

    /**
     * 一時保存画像を本登録
     */
    public static function storeTmpPicture($task_id, $file_path, $created_user_id)
    {
        if(TaskPicture::create([
        'task_id' => $task_id,
        'file_path' => $file_path,
        'created_user_id' => $created_user_id,
        ])){
            $result = ['success' => __('Picture uploaded successfully.')];
        } else {
            $result = ['error' => __('Failed to upload the picture.')];
        };

        return $result;
    }
}