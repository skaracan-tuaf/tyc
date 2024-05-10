<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Munition;
use App\Models\Category;
use App\Models\Image;

class FrontendController extends Controller
{
    public function index()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.home', compact(
            'munitions',
            'categories'
        ));
    }

    public function blog()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.blog', compact(
            'munitions',
            'categories'
        ));
    }

    public function about()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.about', compact(
            'munitions',
            'categories'
        ));
    }

    public function contact()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.contact', compact(
            'munitions',
            'categories'
        ));
    }

    public function show($id)
    {
        $munition = Munition::findOrFail($id);

        return view('Frontend.pages.home', compact(
            'munitions',
            'categories'
        ));
    }

    public function ShowMunitionDetail($slug)
    {
        $categories = Category::all();

        $munition = Munition::where('slug', $slug)->first();

        if (!$munition) {
            abort(404);
        }

        $munitionImages = Image::where('munition_id', $munition->id)->get();

        return view('Frontend.pages.munition_detail', compact('munition', 'munitionImages', 'categories'));
    }

    public function search(Request $request)
    {
        $categories = Category::all();

        // Arama sorgusunu al
        $search = $request->input('q');

        // Munition modelinden arama yap
        $munitions = Munition::where('name', 'like', '%' . $search . '%')
            ->orWhereHas('category', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('origin', 'like', '%' . $search . '%')
            ->orWhere('summary', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->paginate(12); // Sayfalama ekleyerek 12 sonuç göster

        return view('Frontend.pages.home', compact('munitions', 'search', 'categories'));
    }
}
