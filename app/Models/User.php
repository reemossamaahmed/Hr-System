<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        // 'role',
        'position',
        'department_id',
        // 'department',
        'base_salary',
        'hire_date',
        'status',
        'address',
        'national_id',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'base_salary' => 'decimal:2',
            'password' => 'hashed',
            // 'email_verified_at' => 'datetime'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    // public function isHr(): bool
    // {
    //     return $this->role === 'hr';
    // }

    // public function isEmployee(): bool
    // {
    //     return $this->role === 'employee';
    // }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    // public function scopeHr($query)
    // {
    //     return $query->where('role', 'hr');
    // }

    // public function scopeEmployee($query)
    // {
    //     return $query->where('role', 'employee');
    // }

    // public function scopeActive($query)
    // {
    //     return $query->where('status', 'active');
    // }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

}



























