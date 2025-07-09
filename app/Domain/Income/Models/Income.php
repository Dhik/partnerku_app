<?php

namespace App\Domain\Income\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'income';
    
    protected $fillable = [
        'nama_client',
        'date',
        'revenue_contract',
        'service',
        'team_in_charge'
    ];

    protected $casts = [
        'date' => 'date',
        'revenue_contract' => 'decimal:2',
        'team_in_charge' => 'json'  // Cast as JSON to handle array data
    ];
}