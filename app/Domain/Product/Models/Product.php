<?php

namespace App\Domain\Product\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Tenant\Traits\FilterByTenant;

class Product extends Model
{
    use FilterByTenant;

    protected $fillable = [
        'name',
        'tenant_id'
    ];

    /**
     * Get the tenant that owns the product.
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}