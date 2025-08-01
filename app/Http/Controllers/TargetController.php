<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\TargetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TargetController extends Controller
{
    /**
     * Hedef ve kategori yönetimi sayfasını gösterir.
     */
    public function management(Request $request)
    {
        $targets = Target::with(['category', 'subcategory'])->get();
        $targetCategories = TargetCategory::with('parent')->get();

        $editingCategory = null;
        if ($request->has('edit_category')) {
            $editingCategory = TargetCategory::find($request->edit_category);
        }

        return view('Backend.pages.target_management', compact('targets', 'targetCategories', 'editingCategory'));
    }

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

        // Eğer target.management sayfasından geliyorsa veya return_to parametresi varsa oraya yönlendir
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management') ||
            $request->has('return_to') && $request->return_to === 'management') {
            return redirect()->route('target.management')->with('success', $target->name . ' başarıyla eklendi.');
        }

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

        // Eğer target.management sayfasından geliyorsa veya return_to parametresi varsa oraya yönlendir
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management') ||
            $request->has('return_to') && $request->return_to === 'management') {
            return redirect()->route('target.management')->with('success', $target->name . ' başarıyla güncellendi.');
        }

        return redirect()->route('target.index')->with('success', $target->name . ' başarıyla güncellendi.');
    }

    /**
     * Hedef durumunu değiştirir.
     */
    public function changeStatus($id)
    {
        $target = Target::findOrFail($id);
        $target->update(['status' => !$target->status]);

        // Eğer target.management sayfasından geliyorsa oraya yönlendir
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
            return redirect()->route('target.management')->with('success', $target->name . ' durumu değiştirildi.');
        }

        return redirect()->route('target.index')->with('success', $target->name . ' durumu değiştirildi.');
    }

    /**
     * Hedefi siler.
     */
    public function destroy(Target $target)
    {
        $targetName = $target->name;
        $target->delete();

        // Eğer target.management sayfasından geliyorsa oraya yönlendir
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
            return redirect()->route('target.management')->with('success', $targetName . ' silindi.');
        }

        return redirect()->route('target.index')->with('success', $targetName . ' silindi.');
    }
}
