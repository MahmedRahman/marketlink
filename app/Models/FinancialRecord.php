<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialRecord extends Model
{
    protected $fillable = [
        'type',
        'description',
        'amount',
        'currency',
        'payment_status',
        'status',
        'record_date',
        'month',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'record_date' => 'date',
    ];
}
