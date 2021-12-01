<?php

namespace App\Models;

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
}
