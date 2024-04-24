<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Munition extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'slug', 'summary', 'description', 'price', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'munition_attributes')->withPivot('value');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
