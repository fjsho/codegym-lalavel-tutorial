<?php

namespace App\Models;

use Exception;
use League\Flysystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Storage;

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

    /**
     * 課題登録と同時に画像保存を行うメソッド
     * 成功時：登録に成功したtaskレコードのインスタンスを返す
     * 失敗時：エラーを投げる
     */
    public static function createWithPicture(array $attributes = [])
    {
        // 課題登録処理
        if (!$task = Task::create([
            'project_id' => $attributes['project_id'],
            'task_kind_id' => $attributes['task_kind_id'],
            'name' => $attributes['name'],
            'task_detail' => $attributes['task_detail'],
            'task_status_id' => $attributes['task_status_id'],
            'assigner_id' => $attributes['assigner_id'],
            'task_category_id' => $attributes['task_category_id'],
            'due_date' => $attributes['due_date'],
            'created_user_id' => $attributes['created_user_id'],
        ])) {
            throw new Exception(__('Failed to create the task.'));
        }

        if (!is_null($attributes['tmp_files'])) {
            //存在確認処理
            if (Task::tmpFileExists($attributes['tmp_files'])) {
                //移動処理
                $moved_files_path = Task::moveTmpFiles($attributes['tmp_files']);
                //画像登録処理
                TaskPicture::storePictures($task->id, $moved_files_path, $task->created_user_id);
            }
        }
        return $task;
    }

    /**
     * 一時ファイルのパス、またはパスを格納した配列を受け取り、それぞれ存在するか確認する。
     * 全ての画像が存在する場合：tureを返す
     * １枚でも存在しない画像がある場合：例外を投げる
     */
    public static function tmpFileExists(string|array $file_list)
    {
        foreach ($file_list as $file) {
            if (!Storage::exists('public/tmp/' . $file)) {
                throw new FileNotFoundException(__('File not found.'));
            }
        }
        return true;
    }

    /**
     * 一時ファイルの名前、または名前を格納した配列を受け取り、それぞれを公開ディレクトリに移動させる
     * 成功時：移動先のファイルパスを配列で返す
     * 失敗時：エラーを投げる
     */
    public static function moveTmpFiles(string|array $file_list)
    {
        $from_dir_path = 'public/tmp/';
        $to_dir_path = 'public/';
        foreach ($file_list as $file_name) {
            if (Storage::move($from_dir_path . $file_name, $to_dir_path . $file_name)) {
                $file_path_list[] = $file_name;
            } else {
                throw new Exception(__('Failed to move the file.'));
            };
        }
        return $file_path_list;
    }

    /**
     * 担当者とプロジェクトでタスクをフィルタリングし、フィルタリング後のタスクを返す
     */
    public static function getFilteredTasks($assigner_id, $project_id)
    {
        $tasks = Task::select(
            'tasks.*',
            'tasks.id as key,'
        )
            ->with('project');

        if (isset($project_id)) {
            $tasks->join('projects', 'tasks.project_id', 'projects.id');
            $tasks->where('project_id', '=', $project_id);
        }

        if (isset($assigner_id)) {
            $tasks->leftJoin('users as search_assigner', 'tasks.assigner_id', 'search_assigner.id');
            $tasks->where('tasks.assigner_id', '=', $assigner_id);
        }

        $filtered_tasks = $tasks->get();
        return $filtered_tasks;
    }
}
