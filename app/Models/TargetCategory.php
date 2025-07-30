<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TargetCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'parent_id', 'status', 'description', 'image'];

    protected $dates = ['deleted_at'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($targetCategory) {
            $targetCategory->slug = Str::slug($targetCategory->name);
        });

        static::updating(function ($targetCategory) {
            $targetCategory->slug = Str::slug($targetCategory->name);
        });
    }

    public function parent()
    {
        return $this->belongsTo(TargetCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TargetCategory::class, 'parent_id');
    }

}
