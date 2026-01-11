<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'site_name',
        'site_url',
        'username',
        'password',
        'login_type',
        'responsible_phone',
        'subscription_ownership',
        'shared_with_phone',
        'subscription_type',
        'renewal_day',
        'amount',
        'currency',
        'status',
    ];

    /**
     * القيم الافتراضية للحقول
     */
    protected $attributes = [
        'subscription_type' => 'monthly',
        'currency' => 'egp',
        'status' => 'active',
    ];

}
