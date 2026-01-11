<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'phone',
        'status',
        'monthly_salary',
        'order',
    ];

    /**
     * القيم الافتراضية للحقول
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * العلاقة مع المشاريع (Many-to-Many)
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'employee_project')
            ->withPivot('service_types')
            ->withTimestamps();
    }
}
