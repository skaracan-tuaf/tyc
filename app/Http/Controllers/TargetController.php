<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\TargetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TargetController extends Controller
{
    /**
     * Hedef listesini gösterir.
     */
    public function index()
    {
        $targets = Target::with(['category', 'subcategory'])->get();
        return view('Backend.pages.target', compact('targets'));
    }

    /**
     * Yeni hedef ekleme formunu gösterir.
     */
    public function create()
    {
        $categories = TargetCategory::where('status', true)->orderBy('name')->get();
        return view('Backend.pages.target_add_edit', compact('categories'));
    }

    /**
     * Yeni hedefi kaydeder.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:target_categories,id',
            'subcategory_id' => 'nullable|exists:target_categories,id',
            'worth' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $validatedData['slug'] = Str::slug($validatedData['name']);

        $target = Target::create($validatedData);

        return redirect()->route('target.index')->with('success', $target->name . ' başarıyla eklendi.');
    }

    /**
     * Hedef düzenleme formunu gösterir.
     */
    public function edit(Target $target)
    {
        $categories = TargetCategory::where('status', true)->orderBy('name')->get();
        return view('Backend.pages.target_add_edit', compact('target', 'categories'));
    }

    /**
     * Hedefi günceller.
     */
    public function update(Request $request, Target $target)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:target_categories,id',
            'subcategory_id' => 'nullable|exists:target_categories,id',
            'worth' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $validatedData['slug'] = Str::slug($validatedData['name']);

        $target->update($validatedData);

        return redirect()->route('target.index')->with('success', $target->name . ' başarıyla güncellendi.');
    }

    /**
     * Hedef durumunu değiştirir.
     */
    public function changeStatus($id)
    {
        $target = Target::findOrFail($id);
        $target->update(['status' => !$target->status]);
        return redirect()->route('target.index')->with('success', $target->name . ' durumu değiştirildi.');
    }

    /**
     * Hedefi siler.
     */
    public function destroy(Target $target)
    {
        $targetName = $target->name;
        $target->delete();
        return redirect()->route('target.index')->with('success', $targetName . ' silindi.');
    }
}
