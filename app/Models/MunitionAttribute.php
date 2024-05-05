<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MunitionAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['munition_id', 'attribute_id', 'value', 'min', 'max'];

    public function munition()
    {
        return $this->belongsTo(Munition::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
