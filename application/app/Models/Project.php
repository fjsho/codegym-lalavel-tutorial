<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Project extends Model
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
        'key',
        'name',
        'created_user_id',
    ];

    /**
     * ソート対象となる項目.
     *
     * @var array
     */
    public $sortable = [
        'name',
        'key',
        'created_at',
        'updated_at',
    ];

    /**
     * プロジェクトを所有しているユーザーを取得.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * プロジェクトを所有しているユーザーを取得.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
