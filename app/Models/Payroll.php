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

    protected function casts(): array
    {
        return [
            'month' => 'date',
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
