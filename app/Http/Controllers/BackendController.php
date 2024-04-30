<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Munition;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Image;

class BackendController extends Controller
{

    public function index()
    {
        $totalMunitions = Munition::count();
        $totalCategories = Category::count();
        $totalAttributes = Attribute::count();

        $missileImages = Image::count();
        $categoryImages = Category::count();
        $totalImages = $missileImages + $categoryImages;

        return view('Backend.pages.home', compact('totalMunitions', 'totalCategories', 'totalAttributes', 'totalImages'));
    }
}
