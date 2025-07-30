<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Target extends Model
{
   use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'subcategory_id',
        'slug',
        'worth',
        'description',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(TargetCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(TargetCategory::class, 'subcategory_id');
    }
}
