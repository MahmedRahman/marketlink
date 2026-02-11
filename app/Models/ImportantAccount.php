<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportantAccount extends Model
{
    protected $fillable = [
        'site_name',
        'site_url',
        'username',
        'password',
        'notes',
        'month',
    ];

    /**
     * الموظفون المسؤولون عن هذا الحساب (اختيار أكثر من موظف)
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'important_account_employee')
            ->withTimestamps();
    }
}
