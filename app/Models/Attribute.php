<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'option', 'description', 'status'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            $attribute->slug = Str::slug($attribute->name);
        });
    }

    public function munitions()
    {
        return $this->belongsToMany(Munition::class, 'munition_attributes')->withPivot('value');
    }
}
