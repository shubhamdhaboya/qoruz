<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model    
{
    use SoftDeletes;

    CONST STATUS_PENDING = 'Pending';
    CONST STATUS_COMPLETED = 'Completed';

    CONST STATUS = [
        self::STATUS_PENDING, self::STATUS_COMPLETED
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'due_date',
        'parent_id',
        'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Function to listen crud events
     *
     * @return void
     */
    protected static function boot() {
        parent::boot();
        /**
         * Callback fucntion to delete children
         */
        static::deleting(function ($task) {
            $childrenIds = $task->children->pluck('id');
            Task::destroy($childrenIds);
        });
    }

    // parent - child relation
    public function children()
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }
}
