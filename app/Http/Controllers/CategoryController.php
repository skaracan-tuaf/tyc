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
                return redirect()->route('kategori.index')->with('success', $category->name . ' veritabanından eklendi.');
            } else {
                // Kayıt başarısız olursa hata mesajı döndürülür
                return redirect()->route('kategori.index')->with('fail', $category->name . 'veritabanına eklenirken bir hata oluştu.');
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

        // Tüm kategorilerin alınması
        $categories = Category::all();

        return view('Backend.pages.category_add_edit', compact('category', 'categories'));
    }

    /**
     * Change status via button.
     */
    public function changeStatus($id)
    {
        $category = Category::findOrFail($id);

        // Kategorinin durumunu değiştir
        $category->status = !$category->status;
        $category->save();

        // Rekürsif olarak alt kategorilerin durumunu değiştir
        $this->changeStatusRecursive($category, $category->status);

        return redirect()->route('kategori.index')->with('success', ' durumu başarıyla değiştirildi.');
    }

    public function changeStatusRecursive($category, $status)
    {
        // Kategorinin alt kategorilerini al
        $subCategories = $category->children;

        // Her alt kategori için işlemi tekrarla
        foreach ($subCategories as $subCategory) {
            // Alt kategorinin durumunu değiştir
            $subCategory->status = $status;
            $subCategory->save();

            // Alt kategorinin alt kategorileri varsa işlemi tekrarla
            if ($subCategory->children()->exists()) {
                $this->changeStatusRecursive($subCategory, $status);
            }
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
            'image' => $request->hasFile('categoryImage') ? $request->file('categoryImage')->getClientOriginalName() : null,
            'description' => $request->input('description'),
            'status' => $request->input('status') == '1' ? true : false,
        ];

        // Kategori ile üst kategorisinin aynı olup olmadığının kontrolü
        if ($data['parent'] == $category->id) {
            return redirect()->route('kategori.edit', $id)->with('fail', 'Kategori, kendisi ile aynı bir üst kategori olarak güncellenemez.');
        }

        // Doğrulama kurallarının belirlenmesi
        $rules = [
            'name' => 'required|string|max:255',
            'parent' => 'nullable|exists:categories,id',
            'image' => 'nullable|string',
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

            // Kullanıcı resim yüklediyse işlemler yapılır
            if ($request->hasFile('categoryImage')) {

                Storage::delete('public/' . $category->image);

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

            // Kategori kaydedilir ve başarılı mesajı döndürülür
            if ($category->update()) {
                return redirect()->route('kategori.index')->with('success', $category->name . ' güncellendi.');
            } else {
                // Kayıt başarısız olursa hata mesajı döndürülür
                return redirect()->route('kategori.index')->with('fail', ' güncellenirken bir hata oluştu.');
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
        // Kategoriyi bul
        $category = Category::find($id);

        // Kategori bulunamazsa geri dön
        if (!$category) {
            return redirect()->back()->with('error', 'Kategori bulunamadı.');
        }

        // Kategorinin alt kategorilerini silen yardımcı fonksiyon
        function deleteSubCategories($category)
        {
            // Kategorinin alt kategorilerini al
            $subCategories = $category->children()->get();

            // Alt kategorileri dolaş ve her birini sil
            foreach ($subCategories as $subCategory) {
                // Alt kategorinin alt kategorilerini sil (rekürsif olarak)
                deleteSubCategories($subCategory);

                // Alt kategoriyi sil
                $subCategory->delete();

                // Alt kategorinin resmini de sil
                Storage::delete('public/' . $subCategory->image);
            }
        }

        // Alt kategorileri sil (gerekiyorsa)
        deleteSubCategories($category);

        // Ana kategoriyi sil
        if ($category->delete()) {
            // Ana kategorinin resmini de sil
            Storage::delete('public/' . $category->image);
            return redirect()->route('kategori.index')->with('success', $category->name . ' veritabanından silindi.');
        } else {
            return redirect()->route('kategori.index')->with('fail', $category->name . ' veritabanından silinirken bir hata oluştu.');
        }
    }
}
