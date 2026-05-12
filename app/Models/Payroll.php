<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'base_salary',
        'total_late_minutes',
        'deduction',
        'net_salary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
