<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'parent_id', 'status', 'description', 'image'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function munitions()
    {
        return $this->hasMany(Munition::class);
    }

    public function getTotalMunitionsCount()
    {
        $total = $this->munitions()->count();

        foreach ($this->children as $child) {
            $total += $child->munitions()->count(); // Alt kategorinin mühimmat sayısını ekle

            // Alt kategorinin alt kategorilerinin mühimmat sayısını eklemek için recursive olarak getTotalMunitionsCount çağır
            // eğer ortadaki kategoriye ait mühimmat yoksa hesap hatalı çıkıyor.
            foreach ($child->children as $grandChild) {
                $total += $grandChild->getTotalMunitionsCount();
            }
        }

        return $total;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
