<?php

namespace App\Http\Controllers;

use App\Models\TargetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\File;

class TargetCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $targetCategories = Cache::remember('target_categories_all', 60 * 60, fn () => TargetCategory::with('parent')->get());
        return view('Backend.pages.target_category', compact('targetCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $targetCategories = Cache::remember('target_categories_all', 60 * 60, fn () => TargetCategory::all());
        return view('Backend.pages.target_category_add_edit', compact('targetCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $targetCategory = new TargetCategory();
        $targetCategory->name = $data['name'];
        $targetCategory->slug = Str::slug($data['name']);
        $targetCategory->parent_id = $data['parent'];
        $targetCategory->description = $data['description'];
        $targetCategory->status = $data['status'];

        if ($targetCategory->save()) {
            Cache::forget('target_categories_all');

            // Eğer target.management sayfasından geliyorsa oraya yönlendir
            if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
                return redirect()->route('target.management')->with('success', $targetCategory->name . ' başarıyla eklendi.');
            }

            return redirect()->route('target-category.index')->with('success', $targetCategory->name . ' başarıyla eklendi.');
        }

        // Eğer target.management sayfasından geliyorsa oraya yönlendir
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
            return redirect()->route('target.management')->with('fail', $targetCategory->name . ' eklenirken bir hata oluştu.');
        }

        return redirect()->route('target-category.index')->with('fail', $targetCategory->name . ' eklenirken bir hata oluştu.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $targetCategory = TargetCategory::findOrFail($id);
        $targetCategories = Cache::remember('target_categories_all', 60 * 60, fn () => TargetCategory::all());
        return view('Backend.pages.target_category_add_edit', compact('targetCategory', 'targetCategories'));
    }

    /**
     * Change status via button.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id)
    {
        $targetCategory = TargetCategory::findOrFail($id);
        $targetCategory->status = !$targetCategory->status;
        $targetCategory->save();

        $this->changeStatusRecursive($targetCategory, $targetCategory->status);

        Cache::forget('target_categories_all');

        // Eğer target.management sayfasından geliyorsa oraya yönlendir
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
            return redirect()->route('target.management')->with('success', $targetCategory->name . ' durumu başarıyla değiştirildi.');
        }

        return redirect()->route('target-category.index')->with('success', $targetCategory->name . ' durumu başarıyla değiştirildi.');
    }

    /**
     * Recursively change status of subcategories.
     *
     * @param TargetCategory $targetCategory
     * @param bool $status
     * @return void
     */
    private function changeStatusRecursive(TargetCategory $targetCategory, bool $status)
    {
        $subCategories = $targetCategory->children;
        foreach ($subCategories as $subCategory) {
            $subCategory->status = $status;
            $subCategory->save();

            if ($subCategory->children()->exists()) {
                $this->changeStatusRecursive($subCategory, $status);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $targetCategory = TargetCategory::findOrFail($id);

        if ($request->input('parent') == $id) {
            return redirect()->route('target-category.edit', $id)->with('fail', 'Kategori, kendisi ile aynı bir üst kategori olarak güncellenemez.');
        }

        $data = $this->validateRequest($request);

        $targetCategory->name = $data['name'];
        $targetCategory->slug = Str::slug($data['name']);
        $targetCategory->parent_id = $data['parent'];
        $targetCategory->description = $data['description'];
        $targetCategory->status = $data['status'];

        if ($targetCategory->save()) {
            Cache::forget('target_categories_all');

            // Eğer target.management sayfasından geliyorsa oraya yönlendir
            if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
                return redirect()->route('target.management')->with('success', $targetCategory->name . ' başarıyla güncellendi.');
            }

            return redirect()->route('target-category.index')->with('success', $targetCategory->name . ' başarıyla güncellendi.');
        }

        // Eğer target.management sayfasından geliyorsa oraya yönlendir
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
            return redirect()->route('target.management')->with('fail', $targetCategory->name . ' güncellenirken bir hata oluştu.');
        }

        return redirect()->route('target-category.edit', $id)->with('fail', $targetCategory->name . ' güncellenirken bir hata oluştu.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $targetCategory = TargetCategory::findOrFail($id);

        $this->deleteSubCategories($targetCategory);

        if ($targetCategory->delete()) {
            Cache::forget('target_categories_all');

            // Eğer target.management sayfasından geliyorsa oraya yönlendir
            if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
                return redirect()->route('target.management')->with('success', $targetCategory->name . ' başarıyla silindi.');
            }

            return redirect()->route('target-category.index')->with('success', $targetCategory->name . ' başarıyla silindi.');
        }

        // Eğer target.management sayfasından geliyorsa oraya yönlendir
        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'target-management')) {
            return redirect()->route('target.management')->with('fail', $targetCategory->name . ' silinirken bir hata oluştu.');
        }

        return redirect()->route('target-category.index')->with('fail', $targetCategory->name . ' silinirken bir hata oluştu.');
    }

    /**
     * Recursively delete subcategories and their images.
     *
     * @param TargetCategory $targetCategory
     * @return void
     */
    private function deleteSubCategories(TargetCategory $targetCategory)
    {
        foreach ($targetCategory->children as $subCategory) {
            $this->deleteSubCategories($subCategory);
            $subCategory->delete();
        }
    }

    /**
     * Validate the request data.
     *
     * @param Request $request
     * @return array
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'parent' => 'nullable|exists:target_categories,id',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Kategori adı zorunludur.',
        ]);
    }
}
