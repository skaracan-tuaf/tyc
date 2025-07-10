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
            'image' => $request->hasFile('image') ? $request->file('image')->getClientOriginalName() : null,
        ];

        $rules = [
            'name' => 'required|string',
            'type' => 'required|string',
            'origin' => 'required|string|size:2',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'image' => 'nullable|string',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {
            $platform = new Platform();
            $platform->fill($data);

            if ($request->hasFile('platformImage')) {
                // Resmin base64 verisi alınır ve resim dosyası oluşturulur
                $croppedImageData = $request->input('croppedImage');
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));

                // Geçici bir dosya oluşturulur ve resim verisi yazılır
                $tempFilePath = tempnam(sys_get_temp_dir(), 'platform_image_');
                file_put_contents($tempFilePath, $image);

                // Resmin dosya bilgileri alınır ve dosya adı oluşturulur
                $imageData = $request->file('platformImage');
                $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                $imageExtension = $imageData->getClientOriginalExtension();
                $fullImageName = $imageName . '.' . $imageExtension;

                // Geçici dosya Storage'a yüklenir ve dosya yolu kaydedilir
                $storagePath = Storage::putFileAs('public/platform_images', new File($tempFilePath), $fullImageName);
                $platform->image = 'platform_images/' . $fullImageName;
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
            'image' => $request->hasFile('image') ? $request->file('image')->getClientOriginalName() : null,
        ];

        $rules = [
            'name' => 'required|string',
            'type' => 'required|string',
            'origin' => 'required|string|size:2',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'image' => 'nullable|string',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {
            $platform->fill($data);

            if ($request->hasFile('platformImage')) {
                // Kırpılmış base64 veriyi al
                $croppedImageData = $request->input('croppedImage');
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));

                // Geçici dosyaya yaz
                $tempPath = tempnam(sys_get_temp_dir(), 'platform_image_');
                file_put_contents($tempPath, $image);

                // Dosya ismi oluştur
                $imageData = $request->file('platformImage');
                $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                $imageExtension = $imageData->getClientOriginalExtension();
                $fullImageName = $imageName . '.' . $imageExtension;

                // Storage'a yükle
                $storagePath = Storage::putFileAs('public/platform_images', new File($tempPath), $fullImageName);

                // Temizle
                unlink($tempPath);

                // Kaydedilecek path
                $validated['image'] = 'platform_images/' . $fullImageName;
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
