<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $platforms = \App\Models\Platform::all();
        return view('Backend.pages.platform', compact('platforms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = [
            'TR' => 'Türkiye',
            'US' => 'A.B.D',
            'DE' => 'Almanya',
            'FR' => 'Fransa',
            'JP' => 'Japonya',
            'CN' => 'Çin',
            'IN' => 'Hindistan',
            'IL' => 'İsrail',
            'RU' => 'Rusya',
            'UA' => 'Ukrayna',
            'BR' => 'Brezilya',
            'GB' => 'İngiltere',
            'IT' => 'İtalya',
            'ES' => 'İspanya',
            'CA' => 'Kanada',
            'AU' => 'Avustralya',
            'NL' => 'Hollanda',
            'CH' => 'İsviçre',
            'SG' => 'Singapur',
            'SE' => 'İsveç',
            'BE' => 'Belçika',
            'AT' => 'Avusturya',
            'KR' => 'Güney Kore',
        ];
        $types = ['uçak', 'helikopter', 'iha', 'diğer'];
        return view('Backend.pages.platform_add_edit', compact('countries', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:uçak,helikopter,iha,diğer',
            'origin' => 'required|string|size:2',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'status' => 'boolean',
        ]);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('platform_images', 'public');
            $validated['image'] = $imagePath;
        }
        \App\Models\Platform::create($validated);
        return redirect()->route('platform.index')->with('success', 'Platform başarıyla eklendi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Platform $platform)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $platform = \App\Models\Platform::findOrFail($id);
        $countries = [
            'TR' => 'Türkiye',
            'US' => 'A.B.D',
            'DE' => 'Almanya',
            'FR' => 'Fransa',
            'JP' => 'Japonya',
            'CN' => 'Çin',
            'IN' => 'Hindistan',
            'IL' => 'İsrail',
            'RU' => 'Rusya',
            'UA' => 'Ukrayna',
            'BR' => 'Brezilya',
            'GB' => 'İngiltere',
            'IT' => 'İtalya',
            'ES' => 'İspanya',
            'CA' => 'Kanada',
            'AU' => 'Avustralya',
            'NL' => 'Hollanda',
            'CH' => 'İsviçre',
            'SG' => 'Singapur',
            'SE' => 'İsveç',
            'BE' => 'Belçika',
            'AT' => 'Avusturya',
            'KR' => 'Güney Kore',
        ];
        $types = ['uçak', 'helikopter', 'iha', 'diğer'];
        return view('Backend.pages.platform_add_edit', compact('platform', 'countries', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $platform = \App\Models\Platform::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:uçak,helikopter,iha,diğer',
            'origin' => 'required|string|size:2',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'status' => 'boolean',
        ]);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('platform_images', 'public');
            $validated['image'] = $imagePath;
        }
        $platform->update($validated);
        return redirect()->route('platform.index')->with('success', 'Platform başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $platform = \App\Models\Platform::findOrFail($id);
        $platform->delete();
        return redirect()->route('platform.index')->with('success', 'Platform silindi.');
    }
}
