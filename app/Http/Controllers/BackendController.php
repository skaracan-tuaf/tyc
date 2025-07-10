<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Munition;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Image;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\Variant;

class BackendController extends Controller
{

    public function index()
    {
        $totalMunitions = Munition::count();
        $totalCategories = Category::count();
        $totalAttributes = Attribute::count();
        $totalPosts = Post::count();
        $totalTags = Tag::count();
        $totalUsers = User::count();

        $missileImages = Image::count();
        $categoryImages = Category::count();
        $totalImages = $missileImages + $categoryImages;

        return view('Backend.pages.home', compact(
            'totalMunitions',
            'totalCategories',
            'totalAttributes',
            'totalImages',
            'totalPosts',
            'totalTags',
            'totalUsers'
        ));
    }

    public function PageNotFound()
    {
        return view('Backend.pages.404');
    }
}
