<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'employee_id',
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'month',
        'order',
        'tags',
        'task_types'
    ];

    protected $casts = [
        'due_date' => 'date',
        'tags' => 'array',
        'task_types' => 'array',
    ];

    /**
     * العلاقة مع الموظف
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * العلاقة مع المشروع
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * العلاقة مع المهمة الرئيسية
     */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * العلاقة مع المهام الفرعية
     */
    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id')->orderBy('order');
    }
}
