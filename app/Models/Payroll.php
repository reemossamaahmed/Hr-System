<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [

        'user_id',

        'month',

        'year',

        'base_salary',

        'overtime_amount',

        'late_deduction',

        'absence_deduction',

        'net_salary',

        'status',

        'paid_at'

    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'deduction' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
