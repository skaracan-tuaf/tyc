<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    use HasFactory;

    protected $fillable = ['munition_variant_id', 'stock', 'price', 'sku'];

    public function munitionVariant()
    {
        return $this->belongsTo(MunitionVariant::class);
    }
}
