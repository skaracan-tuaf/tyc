<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MunitionVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'variant_id', 'variant_value_id'];

    public function product()
    {
        return $this->belongsTo(Munition::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function variantValue()
    {
        return $this->belongsTo(VariantValue::class);
    }
}
