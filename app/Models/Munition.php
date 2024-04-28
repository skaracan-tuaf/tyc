<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Munition extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'category_id', 'origin', 'price', 'summary', 'description', 'status'];

    public static function boot()
    {
        parent::boot();

        // Model oluşturulduğunda otomatik olarak slug oluştur
        static::creating(function ($munition) {
            $munition->slug = Str::slug($munition->name);
        });
    }

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
