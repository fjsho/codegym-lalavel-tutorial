<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * 投稿された画像の情報をテーブルに登録する
     * 成功時：trueを返す
     * 失敗時：例外を投げる
     */
    public static function storePictures($task_id, $file_path_list, $created_user_id)
    {
        $count_stored_pictures = TaskPicture::where('task_id', '=', $task_id)->count();
        $to_store_pictures = count($file_path_list);
        if ($count_stored_pictures + $to_store_pictures > 5) {
            throw new Exception(__('Please limit the number of attached picture to 5 or less.'));
        } else {
            foreach ($file_path_list as $file_path) {
                $records[] = [
                    'task_id' => $task_id,
                    'file_path' => $file_path,
                    'created_user_id' => $created_user_id,
                ];
            }
            if (!$result = TaskPicture::upsert($records,['id'])) {
                throw new Exception(__('Failed to upload the picture.'));
            }
        }
        return $result;
    }
}
