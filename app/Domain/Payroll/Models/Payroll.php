<?php

namespace App\Domain\Payroll\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
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