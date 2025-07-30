<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::all();
        return view('Backend.pages.platform', compact('platforms'));
    }

    public function create()
    {
        $countries = $this->getCountries();
        $types = $this->getTypes();
        return view('Backend.pages.platform_add_edit', compact('countries', 'types'));
    }

    public function store(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'origin' => $request->input('origin'),
            'description' => $request->input('description'),
            'status' => $request->input('status') == '1' ? true : false,
            'image' => null, // Resim burada null olarak başlatılır
        ];

        $rules = [
            'name' => 'required|string',
            'type' => 'required|string',
            'origin' => 'required|string|size:2',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {
            $platform = new Platform();
            $platform->fill($data);

            // Resim yükleme işlemi
            if ($request->hasFile('platformImage') || $request->input('croppedImage')) {
                $croppedImageData = $request->input('croppedImage');
                if ($croppedImageData) {
                    // Base64 verisi varsa decode et
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));
                    if ($image !== false && strlen($image) > 0) {
                        $imageData = $request->file('platformImage');
                        $imageName = $imageData ? Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid() : uniqid('platform_image_');
                        $imageExtension = $imageData ? $imageData->getClientOriginalExtension() : 'jpg';
                        $fullImageName = $imageName . '.' . $imageExtension;
                        $tempFilePath = tempnam(sys_get_temp_dir(), 'platform_image_');
                        file_put_contents($tempFilePath, $image);
                        Storage::putFileAs('public/platform_images', new File($tempFilePath), $fullImageName);
                        $platform->image = 'platform_images/' . $fullImageName;
                        unlink($tempFilePath);
                    }
                } else if ($request->hasFile('platformImage')) {
                    // Sadece dosya yüklenmişse
                    $imageData = $request->file('platformImage');
                    $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                    $imageExtension = $imageData->getClientOriginalExtension();
                    $fullImageName = $imageName . '.' . $imageExtension;
                    Storage::putFileAs('public/platform_images', $imageData, $fullImageName);
                    $platform->image = 'platform_images/' . $fullImageName;
                }
            }

            if ($platform->save()) {
                return redirect()->route('platform.index')->with('success', $platform->name . ' başarıyla eklendi.');
            } else {
                return redirect()->route('platform.index')->with('fail', 'Platform kaydedilirken hata oluştu.');
            }
        } else {
            $errorMessage = implode("\n", $validator->errors()->all());
            return redirect()->route('platform.create')->with('fail', $errorMessage);
        }
    }

    public function edit($id)
    {
        $platform = Platform::findOrFail($id);
        $countries = $this->getCountries();
        $types = $this->getTypes();
        return view('Backend.pages.platform_add_edit', compact('platform', 'countries', 'types'));
    }

    public function update(Request $request, $id)
    {
        $platform = Platform::findOrFail($id);

        $data = [
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'origin' => $request->input('origin'),
            'description' => $request->input('description'),
            'status' => $request->input('status') == '1' ? true : false,
            'image' => $platform->image, // Mevcut resmi koru
        ];

        $rules = [
            'name' => 'required|string',
            'type' => 'required|string',
            'origin' => 'required|string|size:2',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {
            $platform->fill($data);

            // Resim yükleme işlemi
            if ($request->hasFile('platformImage') || $request->input('croppedImage')) {
                $croppedImageData = $request->input('croppedImage');
                if ($croppedImageData) {
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));
                    if ($image !== false && strlen($image) > 0) {
                        $imageData = $request->file('platformImage');
                        $imageName = $imageData ? Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid() : uniqid('platform_image_');
                        $imageExtension = $imageData ? $imageData->getClientOriginalExtension() : 'jpg';
                        $fullImageName = $imageName . '.' . $imageExtension;
                        $tempFilePath = tempnam(sys_get_temp_dir(), 'platform_image_');
                        file_put_contents($tempFilePath, $image);
                        Storage::putFileAs('public/platform_images', new File($tempFilePath), $fullImageName);
                        $platform->image = 'platform_images/' . $fullImageName;
                        unlink($tempFilePath);
                    }
                } else if ($request->hasFile('platformImage')) {
                    $imageData = $request->file('platformImage');
                    $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                    $imageExtension = $imageData->getClientOriginalExtension();
                    $fullImageName = $imageName . '.' . $imageExtension;
                    Storage::putFileAs('public/platform_images', $imageData, $fullImageName);
                    $platform->image = 'platform_images/' . $fullImageName;
                }
            }

            if ($platform->save()) {
                return redirect()->route('platform.index')->with('success', $platform->name . ' güncellendi.');
            } else {
                return redirect()->route('platform.index')->with('fail', 'Güncellenirken hata oluştu.');
            }
        } else {
            $errorMessage = implode("\n", $validator->errors()->all());
            return redirect()->route('platform.edit', $id)->with('fail', $errorMessage);
        }
    }

    public function destroy($id)
    {
        $platform = Platform::findOrFail($id);
        if ($platform->delete()) {
            Storage::delete('public/' . $platform->image);
            return redirect()->route('platform.index')->with('success', $platform->name . ' silindi.');
        } else {
            return redirect()->route('platform.index')->with('fail', 'Silinirken hata oluştu.');
        }
    }

    private function getCountries()
    {
        return [
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
    }

    private function getTypes()
    {
        return [
            'multi_role_fighter',
            'air_superiority_fighter',
            'bomber',
            'attack_aircraft',
            'reconnaissance_aircraft',
            'electronic_warfare_aircraft',
            'tanker_aircraft',
            'trainer_aircraft',
            'transport_aircraft',
            'attack_helicopter',
            'transport_helicopter',
            'uav',
            'ucav',
            'other',
        ];
    }
}
