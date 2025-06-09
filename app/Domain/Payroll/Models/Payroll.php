<?php

namespace App\Domain\Payroll\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payroll';
    
    protected $fillable = [
        'name',
        'posisi',
        'bulan',
        'salary'
    ];

    protected $casts = [
        'salary' => 'decimal:2'
    ];
}