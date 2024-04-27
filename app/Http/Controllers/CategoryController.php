<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('Backend.pages.category', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('Backend.pages.category_add_edit', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Formdan gelen verilerin toplanması
        $data = [
            'name' => $request->input('name'),
            'parent' => $request->has('parent') ? $request->input('parent') : null,
            'image' => $request->hasFile('categoryImage') ? $request->file('categoryImage')->getClientOriginalName() : null,
            'description' => $request->input('description'),
            'status' => $request->input('status') == '1' ? true : false,
        ];

        // Doğrulama kurallarının belirlenmesi
        $rules = [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|boolean',
            'image' => 'required|string',
            'description' => 'nullable|string',
        ];

        // Veri doğrulamasının yapılması
        $validator = Validator::make($data, $rules);

        // Verinin doğrulamasının geçip geçmediğinin kontrol edilmesi
        if ($validator->passes()) {
            // Kategori modelinin oluşturulması
            $category = new Category();
            $category->name = $data['name'];
            $category->slug = Str::slug($data['name']);
            $category->parent_id = $data['parent'];
            $category->description = $data['description'];
            $category->status = $data['status'];

            // Kullanıcı resim yüklediyse işlemler yapılır
            if ($request->hasFile('categoryImage')) {
                // Resmin base64 verisi alınır ve resim dosyası oluşturulur
                $croppedImageData = $request->input('croppedImage');
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));

                // Geçici bir dosya oluşturulur ve resim verisi yazılır
                $tempFilePath = tempnam(sys_get_temp_dir(), 'category_image_');
                file_put_contents($tempFilePath, $image);

                // Resmin dosya bilgileri alınır ve dosya adı oluşturulur
                $imageData = $request->file('categoryImage');
                $imageName = Str::slug(pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME), '_') . '_' . uniqid();
                $imageExtension = $imageData->getClientOriginalExtension();
                $fullImageName = $imageName . '.' . $imageExtension;

                // Geçici dosya Storage'a yüklenir ve dosya yolu kaydedilir
                $storagePath = Storage::putFileAs('public/category_images', new File($tempFilePath), $fullImageName);
                $category->image = 'category_images/' . $fullImageName;
            }

            // Üst kategori seçildiyse ilişkilendirme yapılır
            if ($request->filled('parentCategory')) {
                $parentCategory = Category::find($data['parent']);
                if ($parentCategory) {
                    $category->parent_id = $parentCategory->id;
                }
            }

            // Kategori kaydedilir ve başarılı mesajı döndürülür
            if ($category->save()) {
                return redirect()->route('kategori.index')->with('success', $category->name . ' başarıyla kategorilere eklendi.');
            } else {
                // Kayıt başarısız olursa hata mesajı döndürülür
                return redirect()->route('kategori.index')->with('fail', 'Kategori eklenirken bir hata oluştu.');
            }
        } else {
            // Doğrulama başarısız olursa hatalar kullanıcıya gösterilir
            $errors = $validator->errors()->all();
            $errorMessage = implode("\n", $errors);
            return redirect()->route('kategori.index')->with('fail', $errorMessage);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Düzenlenecek kategorinin bulunması
        $category = Category::findOrFail($id);

        // Kategoriye ait tüm alt kategorilerin alınması
        $subCategories = $category->children()->get();

        // Tüm kategorilerin alınması
        $categories = Category::all();

        return view('Backend.pages.category_add_edit', compact('category', 'subCategories', 'categories'));
    }

    public function changeStatus($id)
    {
        $category = Category::findOrFail($id);

        // Seçilen kategoriye ait tüm alt kategorileri al
        $subCategories = $category->children()->get();

        // Seçilen kategorinin alt kategorisi varsa
        if ($subCategories->isNotEmpty()) {
            // Alt kategorilere ait status değerlerini güncelle
            foreach ($subCategories as $subCategory) {
                $subCategory->status = !$category->status; // Alt kategori status değerinin tam tersini yap
                $subCategory->save(); // Değişiklikleri kaydet
            }
        }

        if ($category->update(['status' => !$category->status])) {
            return redirect()->route('kategori.index')->with('success', 'Kategori durumu başarıyla değiştirildi.');
        } else {
            return redirect()->route('kategori.index')->with('fail', 'Kategori durumu değiştirilirken bir hata oluştu.');
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Düzenlenecek kategorinin bulunması
        $category = Category::findOrFail($id);

        // Formdan gelen verilerin toplanması
        $data = [
            'name' => $request->input('name'),
            'parent' => $request->has('parent') ? $request->input('parent') : null,
            'description' => $request->input('description'),
            'status' => $request->input('status') == '1' ? true : false,
        ];

        // Doğrulama kurallarının belirlenmesi
        $rules = [
            'name' => 'required|string|max:255',
            'parent' => 'nullable|exists:categories,id',
            'status' => 'required|boolean',
            'description' => 'nullable|string',
        ];

        // Veri doğrulamasının yapılması
        $validator = Validator::make($data, $rules);

        // Verinin doğrulamasının geçip geçmediğinin kontrol edilmesi
        if ($validator->passes()) {
            // Kategoriye ait özelliklerin güncellenmesi
            $category->name = $data['name'];
            $category->slug = Str::slug($data['name']);
            $category->parent_id = $data['parent'];
            $category->description = $data['description'];
            $category->status = $data['status'];

            // Kategori kaydedilir ve başarılı mesajı döndürülür
            if ($category->save()) {
                return redirect()->route('kategori.index')->with('success', $category->name . ' başarıyla güncellendi.');
            } else {
                // Kayıt başarısız olursa hata mesajı döndürülür
                return redirect()->route('kategori.index')->with('fail', 'Kategori güncellenirken bir hata oluştu.');
            }
        } else {
            // Doğrulama başarısız olursa hatalar kullanıcıya gösterilir
            $errors = $validator->errors()->all();
            $errorMessage = implode("\n", $errors);
            return redirect()->route('kategori.edit', $id)->with('fail', $errorMessage);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Mesaj bulunamadı.');
        }

        if ($category->delete()) {
            Storage::delete('public/' . $category->image);
            return redirect()->route('kategori.index')->with('success', $category->name . ' başarıyla kategorilerden silindi.');
        } else {
            return redirect()->route('kategori.index')->with('fail', $category->name . 'kategorilerden silinirken bir hata oluştu.');
        }
    }
}
