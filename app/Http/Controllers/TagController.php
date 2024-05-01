<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return view('Backend.pages.tag', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Backend.pages.tag_add_edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:tags,name',
            'slug' => 'nullable|string',
            'status' => 'boolean'
        ]);

        // Slug oluştur
        $slug = Str::slug($validatedData['name']);
        $validatedData['slug'] = Str::slug($slug);

        // Yeni özelliği oluştur
        $tag = Tag::create($validatedData);

        if ($tag) {
            return redirect()->route('etiket.index')->with('success', $tag->name . ' başarıyla veritabanına eklendi.');
        } else {
            return redirect()->route('etiket.index')->with('fail', $tag->name . ' özelliği eklenirken bir hata oluştu.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tag = Tag::findOrFail($id);

        return view('Backend.pages.tag_add_edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:tags,name',
            'slug' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $slug = Str::slug($validatedData['name']);
        $validatedData['slug'] = Str::slug($slug);

        $tag = Tag::findOrFail($id);

        if ($tag->update($validatedData)) {
            return redirect()->route('etiket.index')->with('success', $tag->name . ' başarıyla veritabanında güncellendi.');
        } else {
            return redirect()->route('etiket.index')->with('fail', $tag->name . ' veritabanında güncellenirken bir hata oluştu.');
        }
    }

    public function changeStatus($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update(['status' => !$tag->status]);
        return redirect()->route('etiket.index')->with('success', $tag->name . ' durumu başarıyla değiştirildi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return redirect()->back()->with('error', ' bulunamadı.');
        }

        if ($tag->delete()) {
            return redirect()->route('etiket.index')->with('success', $tag->name . ' başarıyla verıtabanından silindi.');
        } else {
            return redirect()->route('etiket.index')->with('fail', $tag->name . ' veritabanından silinirken bir hata oluştu.');
        }
    }
}
