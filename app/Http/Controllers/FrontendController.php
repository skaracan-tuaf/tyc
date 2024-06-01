<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Munition;
use App\Models\MunitionAttribute;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Attribute;
use App\Models\Image;

class FrontendController extends Controller
{
    public function index()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();
        $tags = Tag::all();

        return view('Frontend.pages.home', compact(
            'munitions',
            'categories',
            'tags'
        ));
    }

    public function kiyasla(Request $request)
    {
        $categories = Category::all();
        $attributes = Attribute::all();
        $tags = Tag::all();

        $targetType = $request->input('target_type');
        $minRange = $request->input('min');
        $maxRange = $request->input('max');

        // Mühimmatları filtrele
        $attribute = Attribute::where('name', 'like', '%menzil%')->first();

        if ($attribute) {
            $munitionIds = MunitionAttribute::where('attribute_id', $attribute->id)
                ->whereBetween('value', [$minRange, $maxRange])
                ->pluck('munition_id');

            $munitions = Munition::whereIn('id', $munitionIds)
                ->where('target_type', $targetType)
                ->get();
        } else {
            $munitions = collect();
        }

        return view('Frontend.pages.munition_compare', compact(
            'munitions',
            'categories',
            'tags',
            'attributes'
        ));
    }



    public function blog()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.blog', compact(
            'munitions',
            'categories'
        ));
    }

    public function about()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.about', compact(
            'munitions',
            'categories'
        ));
    }

    public function contact()
    {
        $munitions = Munition::paginate(9);
        $categories = Category::all();

        return view('Frontend.pages.contact', compact(
            'munitions',
            'categories'
        ));
    }

    public function show($id)
    {
        $munition = Munition::findOrFail($id);

        return view('Frontend.pages.home', compact(
            'munitions',
            'categories'
        ));
    }

    public function ShowMunitionDetail($slug)
    {
        $categories = Category::all();

        $munition = Munition::where('slug', $slug)->first();

        if (!$munition) {
            abort(404);
        }

        $munitionImages = Image::where('munition_id', $munition->id)->get();

        return view('Frontend.pages.munition_detail', compact('munition', 'munitionImages', 'categories'));
    }

    public function getMunitionsByCategory($categoryId)
    {
        $munitions = collect(); // Mühimmatları toplamak için bir koleksiyon oluştur

        // Verilen kategori ID'sine ait tüm mühimmatları al
        $munitions = Munition::where('category_id', $categoryId)->get();

        // Verilen kategoriye ait alt kategorileri bul
        $subCategories = Category::where('parent_id', $categoryId)->get();

        // Her bir alt kategori için mühimmatları almak üzere özyinelemeli olarak bu işlemi tekrarla
        foreach ($subCategories as $subCategory) {
            $munitions = $munitions->merge($this->getMunitionsByCategory($subCategory->id));
        }

        return $munitions;
    }

    public function FilterByCategory($slug)
    {
        //ayrı bir sayfa yapılacak.

        // Kategoriyi bul
        $category = Category::where('slug', $slug)->first();

        // Kategori bulunamazsa 404 hatası göster
        if (!$category) {
            abort(404);
        }

        // Kategoriye ait mühimmatları çek
        //$munitions = Munition::where('category_id', $category->id)->get();

        // Kategoriye ait tüm mühimmatları al (alt kategoriler de dahil)
        $munitions = $this->getMunitionsByCategory($category->id);

        // Mühimmatları bulunamazsa boş dizi döndür
        if ($munitions->isEmpty()) {
            return view('Frontend.pages.home', compact('category'));
        }

        // Mühimmatların resimlerini çekmek için boş bir dizi oluştur
        $munitionImages = [];

        // Her mühimmat için resimleri çek
        foreach ($munitions as $munition) {
            $images = Image::where('munition_id', $munition->id)->get();
            $munitionImages[$munition->id] = $images;
        }

        // Sonucu görünüme aktar ve home.blade.php dosyasını çağır
        return view('Frontend.pages.home', compact('munitions', 'munitionImages', 'category'));
    }


    public function search(Request $request)
    {
        $categories = Category::all();

        // Arama sorgusunu al
        $search = $request->input('q');

        // Munition modelinden arama yap
        $munitions = Munition::where('name', 'like', '%' . $search . '%')
            ->orWhereHas('category', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('origin', 'like', '%' . $search . '%')
            ->orWhere('summary', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->paginate(12); // Sayfalama ekleyerek 12 sonuç göster

        return view('Frontend.pages.home', compact('munitions', 'search', 'categories'));
    }
}
