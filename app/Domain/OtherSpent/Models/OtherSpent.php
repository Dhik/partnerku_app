<?php

namespace App\Domain\OtherSpent\Models;

use Illuminate\Database\Eloquent\Model;

class OtherSpent extends Model
{
    protected $table = 'other_spent';
    
    protected $fillable = [
        'date',
        'detail',
        'amount',
        'evidence_link',
        'type'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];
}