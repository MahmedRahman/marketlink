<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'customer_id',
        'name',
        'service_type',
        'service_revenue',
        'status',
    ];

    /**
     * القيم الافتراضية للحقول
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * العلاقة مع العميل
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * العلاقة مع الموظفين (Many-to-Many)
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_project')
            ->withPivot('service_types')
            ->withTimestamps();
    }

    /**
     * Cast service_type and service_revenue to array
     */
    protected $casts = [
        'service_type' => 'array',
        'service_revenue' => 'array',
    ];
}
