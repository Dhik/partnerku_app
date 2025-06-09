<?php

namespace App\Domain\OtherSpent\Models;

use Illuminate\Database\Eloquent\Model;

class OtherSpent extends Model
{
    protected $fillable = [
        'date',
        'detail',
        'amount',
        'evidence_link'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];
}