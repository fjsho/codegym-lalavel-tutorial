<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'name',
        'task_kind_id',
        'task_detail',
        'task_status_id',
        'created_user_id',
        'updated_user_id',
        'assigner_id',
        'task_category_id',
        'due_date',
        'task_resolution_id',
    ];

    /**
     * ソート対象となる項目.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'task_kind',
        'name',
        'assigner',
        'created_at',
        'due_date',
        'updated_at',
        'user',
    ];

    /**
     * 日付を変形する属性.
     *
     * @var array
     */
    protected $dates = [
        'due_date',
    ];

    /**
     * ユーザーのフルネーム取得.
     */
    public function getKeyAttribute()
    {
        return "{$this->project->key}-{$this->id}";
    }

    /**
     * 課題を所有しているプロジェクトを取得.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * 課題種別を取得.
     */
    public function task_kind()
    {
        return $this->belongsTo(TaskKind::class, 'task_kind_id');
    }

    /**
     * 課題状態を取得.
     */
    public function task_status()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    /**
     * 課題を所有しているユーザーを取得.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * 課題を更新したユーザーを取得.
     */
    public function updated_user()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    /**
     * 課題の担当者を取得.
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigner_id');
    }

    /**
     * 課題カテゴリーを取得.
     */
    public function task_category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    /**
     * 課題の完了理由を取得.
     */
    public function task_resolution()
    {
        return $this->belongsTo(TaskResolution::class, 'task_resolution_id');
    }

    /**
     * 課題のコメントを取得.
     */
    public function task_comments()
    {
        return $this->hasMany(Taskcomment::class, 'task_id');
    }

    public static function moveAndStorePicture($session, $task, $created_user_id)
    {
        // １セッションがtmpfileを持つか確認
        // ２持っていなかったら何もしない
        // ３持っていたらtmpfileのファイル名を取得
        // 以下４−７のループ開始
        // ４tmpディレクトリからpublicディレクトリに移動する→メソッド化済み
        // ５移動後のパスを取得
        // ６画像情報を登録→メソッド化済み
        // ７登録した結果を取得
        // ループ終了
        // ８取得した登録結果にエラーがあればエラーメッセージを取得
        if($session->has('tmp_files')) {
            $tmp_file_names = array_keys(session('tmp_files'));
            foreach($tmp_file_names as $tmp_file_name){
                $tmp_file_path = TaskPicture::movePictureToPublicFromTmp($tmp_file_name);
                $result[] = TaskPicture::storePicture($task->id, $tmp_file_path, $created_user_id);
            }
            // if(in_array('error', $result, true)){
            //     $flash = array_merge($flash,array('error' => __('Failed to upload the picture.')));
            // } 
        }
        return $result;
    }

}
