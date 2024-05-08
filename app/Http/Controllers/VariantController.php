<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $variants = Variant::all();
        return view('Backend.pages.variant', compact('variants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Backend.pages.variant_add_edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $variant = Variant::create([
            'name' => $request->input('name')
        ]);

        $values = $request->input('values');

        if ($values) {
            foreach ($values as $value) {
                $variant->values()->create([
                    'value' => $value
                ]);
            }
        }

        return redirect()->route('varyant.index')->with('success', $variant->name . ' veritabanına eklendi.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Variant $variant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $variant = Variant::findOrFail($id);
        return view('Backend.pages.variant_add_edit', compact('variant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $variant = Variant::findOrFail($id);

        $variant->update([
            'name' => $request->input('name')
        ]);

        $values = $request->input('values');

        // Eski varyant değerlerini sil
        $variant->values()->delete();

        // Yeni varyant değerlerini ekle
        if ($values) {
            foreach ($values as $value) {
                $variant->values()->create([
                    'value' => $value
                ]);
            }
        }

        return redirect()->route('varyant.index')->with('success', $variant->name . ' başarıyla güncellendi.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $variant = Variant::find($id);
        if (!$variant) {
            return redirect()->back()->with('error', 'Varyant bulunamadı.');
        }

        if ($variant->delete()) {
            return redirect()->route('varyant.index')->with('success', $variant->name . ' veritabanından silindi.');
        } else {
            return redirect()->route('varyant.index')->with('fail', $variant->name . ' veritabanından silinirken bir hata oluştu.');
        }
    }
}
