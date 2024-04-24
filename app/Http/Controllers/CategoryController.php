<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('Backend.pages.category', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Backend.pages.category_add_edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    public function changeStatus($id)
    {
        $category = Category::findOrFail($id);

        // Seçilen kategoriye ait tüm alt kategorileri al
        $subCategories = $category->children()->get();

        // Seçilen kategorinin alt kategorisi varsa
        if ($subCategories->isNotEmpty()) {
            // Alt kategorilere ait status değerlerini güncelle
            foreach ($subCategories as $subCategory) {
                $subCategory->status = !$category->status; // Alt kategori status değerinin tam tersini yap
                $subCategory->save(); // Değişiklikleri kaydet
            }
        }

        $category->update(['status' => !$category->status]);
        return redirect()->route('kategori.index')->with('success', 'Kategori durumu başarıyla değiştirildi.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
