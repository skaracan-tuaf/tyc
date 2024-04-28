<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Munition;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class MunitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $munitions = Munition::all();
        return view('Backend.pages.munition', compact('munitions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $munitions = Munition::all();
        return view('Backend.pages.munition_add_edit', compact('categories', 'munitions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'origin' => 'nullable|string',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        // Create a slug
        $slug = Str::slug($validatedData['name']);
        $validatedData['slug'] = $slug;

        // Create the munition
        $munition = Munition::create($validatedData);

        // Process and save images
        for ($i = 1; $i <= 6; $i++) {
            $imageName = 'munitionImage' . $i;
            $imagePath = 'imagePath' . $i;
            $this->processAndSaveImage($request, $munition, $imageName, $imagePath);
        }

        // Redirect to index page with success message
        return redirect()->route('muhimmat.index')->with('success', $munition->name . ' mühimmatı başarıyla oluşturuldu.');
    }

    private function processAndSaveImage($request, $munition, $imageName, $imagePath)
    {
        if ($request->hasFile($imageName)) {
            $file = $request->file($imageName);
            $fileName = $this->generateUniqueFileName($file->getClientOriginalExtension());

            // Save the file to storage
            $file->storeAs('public/munition_images', $fileName);

            // Create and save image record
            $munitionImage = new Image();
            $munitionImage->munition_id = $munition->id;
            $munitionImage->url = 'munition_images/' . $fileName;
            $munitionImage->save();

            // Set the image path in the request
            $request->merge([$imagePath => 'munition_images/' . $fileName]);
        }
    }

    private function generateUniqueFileName($extension)
    {
        // Generate a unique filename
        $uniqueName = uniqid();
        return 'munition_image_' . time() . '_' . $uniqueName . '.' . $extension;
    }
    /**
     * Display the specified resource.
     */
    public function show(Munition $munition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = Category::all();
        $munition = Munition::findOrFail($id);

        return view('Backend.pages.munition_add_edit', compact('categories', 'munition'));
    }

    public function changeStatus($id)
    {
        $munition = Munition::findOrFail($id);
        $munition->update(['status' => !$munition->status]);
        return redirect()->route('muhimmat.index')->with('success', $munition->name . ' durumu başarıyla değiştirildi.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Gelen isteği doğrula
        $validatedData = $request->validate([
            'name' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'origin' => 'nullable|string',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        // Slug oluştur
        $slug = Str::slug($validatedData['name']);
        $validatedData['slug'] = $slug;

        // Mühimmatı güncelle
        $munition = Munition::findOrFail($id);
        $munition->update($validatedData);

        // Başarıyla güncellendiğine dair mesajla birlikte index sayfasına yönlendir
        return redirect()->route('muhimmat.index')->with('success', 'Mühimmat başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $munition = Munition::find($id);
        if (!$munition) {
            return redirect()->back()->with('error', ' muhimmat bulunamadı.');
        }

        $munitionImages = Image::where('munition_id', $id)->get();

        // Storage'dan dosyaları sil
        foreach ($munitionImages as $munitionImage) {
            Storage::delete('public/' . $munitionImage->url);
        }

        if ($munition->delete()) {
            return redirect()->route('muhimmat.index')->with('success', $munition->name . ' başarıyla muhimmatlardan silindi.');
        } else {
            return redirect()->route('muhimmat.index')->with('fail', $munition->name . ' muhimmatı silinirken bir hata oluştu.');
        }
    }
}
