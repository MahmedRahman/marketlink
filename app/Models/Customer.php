<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'phone',
        'status',
    ];

    /**
     * القيم الافتراضية للحقول
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * العلاقة مع المشاريع
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
