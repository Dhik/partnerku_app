<?php

namespace App\Domain\Niche\Models;

use Illuminate\Database\Eloquent\Model;

class Niche extends Model
{
    protected $table = 'niche';
    
    protected $fillable = [
        'name'
    ];
}