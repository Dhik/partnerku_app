<?php

namespace App\Domain\Income\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'income';
    
    protected $fillable = [
        'nama_client',
        'revenue_contract',
        'service',
        'team_in_charge'
    ];

    protected $casts = [
        'revenue_contract' => 'decimal:2',
        'team_in_charge' => 'string'  // Explicitly cast as string
    ];
}