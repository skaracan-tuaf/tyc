<?php

namespace App\Http\Controllers;

use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $targets = Target::all();
        return view('Backend.pages.target', compact('targets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getNatoTargetCategories();
        return view('Backend.pages.target_add_edit', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'subcategory' => 'nullable|string|max:255',
            'worth' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validatedData['slug'] = Str::slug($validatedData['name']);

        Target::create($validatedData);

        return redirect()->route('target.index')->with('success', 'Hedef başarıyla eklendi.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Target $target)
    {
        $categories = $this->getNatoTargetCategories();
        return view('Backend.pages.target_add_edit', compact('target', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Target $target)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'subcategory' => 'nullable|string|max:255',
            'worth' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validatedData['slug'] = Str::slug($validatedData['name']);

        $target->update($validatedData);

        return redirect()->route('target.index')->with('success', 'Hedef başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Target $target)
    {
        $target->delete();
        return redirect()->route('target.index')->with('success', 'Hedef silindi.');
    }

    /**
     * NATO standardına göre hedef kategorileri.
     */
    private function getNatoTargetCategories(): array
    {
        return [
            'Surface Targets (Yer Hedefleri)' => [
                'Fixed Installations (Sabit Tesisler)',
                'Armored Vehicles (Zırhlı Araçlar)',
                'Artillery (Topçu Birlikleri)',
                'Infrastructure (Altyapı)',
                'Radar Installations (Radar Tesisleri)',
                'Airfields (Hava Alanları)',
                'Fuel Depots (Yakıt Depoları)',
                'Bridges (Köprüler)',
                'Power Stations (Enerji Santralleri)',
            ],

            'Moving Ground Targets (Hareketli Kara Hedefleri)' => [
                'Convoys (Araç Konvoyları)',
                'Infantry (Piyade Birlikleri)',
                'Mobile SAMs (Mobil Hava Savunma)',
                'Logistics (Lojistik Araçları)',
                'Reconnaissance Units (Keşif Birlikleri)',
                'Engineering Vehicles (İstihkam Araçları)',
            ],

            'Air Targets (Hava Hedefleri)' => [
                'Fixed-Wing Aircraft (Sabit Kanatlı Uçaklar)',
                'Rotary-Wing Aircraft (Döner Kanatlı Uçaklar)',
                'UAV/Drone (İnsansız Hava Araçları)',
                'Bomber Aircraft (Bombardıman Uçakları)',
                'Airborne Early Warning (Havadan Erken İhbar)',
                'Transport Aircraft (Nakliye Uçakları)',
            ],

            'Naval Targets (Deniz Hedefleri)' => [
                'Surface Ships (Yüzey Gemileri)',
                'Submarines (Denizaltılar)',
                'Amphibious Assault Ships (Çıkarma Gemileri)',
                'Aircraft Carriers (Uçak Gemileri)',
                'Frigates (Fırkateynler)',
                'Mine Warfare Ships (Mayın Gemileri)',
            ],

            'Command & Control Targets (Komuta ve Kontrol Hedefleri)' => [
                'HQ Buildings (Karargâh Binaları)',
                'Communication Centers (İletişim Merkezleri)',
                'Radar Stations (Radar İstasyonları)',
                'Mobile Command Posts (Mobil Komuta Merkezleri)',
                'Command Bunkers (Komuta Sığınakları)',
            ],

            'Strategic Assets (Stratejik Varlıklar)' => [
                'Nuclear Facilities (Nükleer Tesisler)',
                'Missile Silos (Füze Silo Tesisleri)',
                'Satellite Uplinks (Uydu Bağlantıları)',
                'Cyber Infrastructure (Siber Altyapı)',
            ],
        ];
    }
}
