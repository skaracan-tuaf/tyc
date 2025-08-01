<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Munition;
use App\Models\Post;
use App\Models\MunitionAttribute;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Attribute;
use App\Models\Image;
use App\Models\Platform;
use App\Models\Target;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FrontendController extends Controller
{
    /**
     * Get common data for views to avoid repetition.
     *
     * @return array
     */
    private function getCommonViewData(): array
    {
        return [
            'categories' => Category::all(),
            'tags' => Tag::all(),
            'platforms' => Platform::all(),
            'targets' => Target::all(),
            'targetTypes' => Munition::distinct()->pluck('target_type'),
        ];
    }

    /**
     * Display the homepage with paginated munitions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $munitions = Munition::paginate(12);

        return view('Frontend.pages.home', array_merge(
            $this->getCommonViewData(),
            compact('munitions')
        ));
    }

    /**
     * Show results based on target and weather conditions
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function sonuclariGoster(Request $request)
    {
        $request->validate([
            'category_id' => 'required|numeric',
            'target_type' => 'required|numeric',
            'weather' => 'required|string|in:Açık,Sisli,Yağmurlu',
        ]);

        $categoryId = $request->input('category_id');
        $targetId = $request->input('target_type');
        $weather = $request->input('weather');

        // Kategori ve hedef bilgilerini al
        $category = Category::find($categoryId);
        $target = Target::find($targetId);
        $categoryName = $category ? $category->name : 'Bilinmeyen Kategori';
        $targetName = $target ? $target->name : 'Bilinmeyen Hedef';

        //dd($categoryName);

        // Statik veri - kategori, hedef adı ve meteorolojik duruma göre sonuçlar
        $staticData = [
            'Hava Hava' => [
                'Pist' => [
                    'Açık' => [
                        ['name' => 'AIM 120B', 'rank' => 1, 'cost' => '$500K', 'platform' => 'F-16'],
                        ['name' => 'GÖKDOĞAN', 'rank' => 2, 'cost' => '$300K', 'platform' => 'AKINCI'],
                        ['name' => 'BOZDOĞAN', 'rank' => 3, 'cost' => '$250K', 'platform' => 'HÜRJET'],
                    ],
                    'Sisli' => [
                        ['name' => 'GÖKDOĞAN', 'rank' => 1, 'cost' => '$300K', 'platform' => 'AKINCI'],
                        ['name' => 'AIM 120C', 'rank' => 2, 'cost' => '$600K', 'platform' => 'F-16'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'BOZDOĞAN', 'rank' => 1, 'cost' => '$250K', 'platform' => 'HÜRJET'],
                        ['name' => 'AIM 120B', 'rank' => 2, 'cost' => '$500K', 'platform' => 'F-16'],
                    ],
                ],
                'Radar' => [
                    'Açık' => [
                        ['name' => 'AIM 120B', 'rank' => 1, 'cost' => '$500K', 'platform' => 'F-16'],
                        ['name' => 'GÖKDOĞAN', 'rank' => 2, 'cost' => '$300K', 'platform' => 'AKINCI'],
                    ],
                    'Sisli' => [
                        ['name' => 'GÖKDOĞAN', 'rank' => 1, 'cost' => '$300K', 'platform' => 'AKINCI'],
                        ['name' => 'AIM 120C', 'rank' => 2, 'cost' => '$600K', 'platform' => 'F-16'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'BOZDOĞAN', 'rank' => 1, 'cost' => '$250K', 'platform' => 'HÜRJET'],
                    ],
                ],
                'Komuta Merkezi' => [
                    'Açık' => [
                        ['name' => 'AIM 120C', 'rank' => 1, 'cost' => '$600K', 'platform' => 'F-16'],
                        ['name' => 'BOZDOĞAN', 'rank' => 2, 'cost' => '$250K', 'platform' => 'HÜRJET'],
                    ],
                    'Sisli' => [
                        ['name' => 'GÖKDOĞAN', 'rank' => 1, 'cost' => '$300K', 'platform' => 'AKINCI'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'AIM 120B', 'rank' => 1, 'cost' => '$500K', 'platform' => 'F-16'],
                    ],
                ],
                'Hangar' => [
                    'Açık' => [
                        ['name' => 'BOZDOĞAN', 'rank' => 1, 'cost' => '$250K', 'platform' => 'HÜRJET'],
                        ['name' => 'AIM 120B', 'rank' => 2, 'cost' => '$500K', 'platform' => 'F-16'],
                    ],
                    'Sisli' => [
                        ['name' => 'GÖKDOĞAN', 'rank' => 1, 'cost' => '$300K', 'platform' => 'AKINCI'],
                        ['name' => 'BOZDOĞAN', 'rank' => 2, 'cost' => '$250K', 'platform' => 'HÜRJET'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'AIM 120C', 'rank' => 1, 'cost' => '$600K', 'platform' => 'F-16'],
                    ],
                ],
            ],
            'Hava Yer (Füze)' => [
                'Pist' => [
                    'Açık' => [
                        ['name' => 'SOM-A', 'rank' => 1, 'cost' => '$800K', 'platform' => 'F-16'],
                        ['name' => 'SOM-B1', 'rank' => 2, 'cost' => '$750K', 'platform' => 'F-16'],
                        ['name' => 'SOM-J', 'rank' => 3, 'cost' => '$700K', 'platform' => 'AKINCI'],
                    ],
                    'Sisli' => [
                        ['name' => 'SOM-B1', 'rank' => 1, 'cost' => '$750K', 'platform' => 'F-16'],
                        ['name' => 'SOM-A', 'rank' => 2, 'cost' => '$800K', 'platform' => 'F-16'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'SOM-J', 'rank' => 1, 'cost' => '$700K', 'platform' => 'AKINCI'],
                        ['name' => 'SOM-A', 'rank' => 2, 'cost' => '$800K', 'platform' => 'F-16'],
                    ],
                ],
                'Radar' => [
                    'Açık' => [
                        ['name' => 'SOM-A', 'rank' => 1, 'cost' => '$800K', 'platform' => 'F-16'],
                        ['name' => 'SOM-B1', 'rank' => 2, 'cost' => '$750K', 'platform' => 'F-16'],
                    ],
                    'Sisli' => [
                        ['name' => 'SOM-B1', 'rank' => 1, 'cost' => '$750K', 'platform' => 'F-16'],
                        ['name' => 'SOM-J', 'rank' => 2, 'cost' => '$700K', 'platform' => 'AKINCI'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'SOM-J', 'rank' => 1, 'cost' => '$700K', 'platform' => 'AKINCI'],
                    ],
                ],
                'Komuta Merkezi' => [
                    'Açık' => [
                        ['name' => 'SOM-B1', 'rank' => 1, 'cost' => '$750K', 'platform' => 'F-16'],
                        ['name' => 'SOM-A', 'rank' => 2, 'cost' => '$800K', 'platform' => 'F-16'],
                    ],
                    'Sisli' => [
                        ['name' => 'SOM-J', 'rank' => 1, 'cost' => '$700K', 'platform' => 'AKINCI'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'SOM-A', 'rank' => 1, 'cost' => '$800K', 'platform' => 'F-16'],
                    ],
                ],
                'Hangar' => [
                    'Açık' => [
                        ['name' => 'SOM-J', 'rank' => 1, 'cost' => '$700K', 'platform' => 'AKINCI'],
                        ['name' => 'SOM-A', 'rank' => 2, 'cost' => '$800K', 'platform' => 'F-16'],
                    ],
                    'Sisli' => [
                        ['name' => 'SOM-B1', 'rank' => 1, 'cost' => '$750K', 'platform' => 'F-16'],
                        ['name' => 'SOM-J', 'rank' => 2, 'cost' => '$700K', 'platform' => 'AKINCI'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'SOM-B1', 'rank' => 1, 'cost' => '$750K', 'platform' => 'F-16'],
                    ],
                ],
            ],
            'Hava Yer (Bomba)' => [
                'Pist' => [
                    'Açık' => [
                        ['name' => 'AKYA', 'rank' => 1, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                        ['name' => 'ATMACA', 'rank' => 2, 'cost' => '$900K', 'platform' => 'Korvet'],
                    ],
                    'Sisli' => [
                        ['name' => 'ATMACA', 'rank' => 1, 'cost' => '$900K', 'platform' => 'Korvet'],
                        ['name' => 'AKYA', 'rank' => 2, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'AKYA', 'rank' => 1, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                    ],
                ],
                'Radar' => [
                    'Açık' => [
                        ['name' => 'AKYA', 'rank' => 1, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                        ['name' => 'ATMACA', 'rank' => 2, 'cost' => '$900K', 'platform' => 'Korvet'],
                    ],
                    'Sisli' => [
                        ['name' => 'ATMACA', 'rank' => 1, 'cost' => '$900K', 'platform' => 'Korvet'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'AKYA', 'rank' => 1, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                    ],
                ],
                'Komuta Merkezi' => [
                    'Açık' => [
                        ['name' => 'ATMACA', 'rank' => 1, 'cost' => '$900K', 'platform' => 'Korvet'],
                        ['name' => 'AKYA', 'rank' => 2, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                    ],
                    'Sisli' => [
                        ['name' => 'AKYA', 'rank' => 1, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'ATMACA', 'rank' => 1, 'cost' => '$900K', 'platform' => 'Korvet'],
                    ],
                ],
                'Hangar' => [
                    'Açık' => [
                        ['name' => 'AKYA', 'rank' => 1, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                        ['name' => 'ATMACA', 'rank' => 2, 'cost' => '$900K', 'platform' => 'Korvet'],
                    ],
                    'Sisli' => [
                        ['name' => 'ATMACA', 'rank' => 1, 'cost' => '$900K', 'platform' => 'Korvet'],
                        ['name' => 'AKYA', 'rank' => 2, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'AKYA', 'rank' => 1, 'cost' => '$1.2M', 'platform' => 'Denizaltı'],
                    ],
                ],
            ],
        ];

        // Seçilen kategori, hedef adı ve meteorolojik duruma göre sonuçları al
        $results = $staticData[$categoryName][$targetName][$weather] ?? [];

        //dd($request);

        return view('Frontend.pages.results', array_merge(
            $this->getCommonViewData(),
            compact('results', 'categoryName', 'targetName', 'weather')
        ));
    }

    /**
     * Compare munitions based on filters.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function kiyasla(Request $request)
    {
        $request->validate([
            'target_type' => 'nullable|string|max:255',
            'min' => 'nullable|numeric|min:0',
            'max' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $targetType = $request->input('target_type');
        $minRange = $request->input('min');
        $maxRange = $request->input('max');
        $categoryId = $request->input('category_id');

        $query = Munition::query();

        // Filter by range if provided
        if ($minRange || $maxRange) {
            $attribute = Attribute::where('name', 'like', '%menzil%')->first();

            if ($attribute) {
                $munitionQuery = MunitionAttribute::where('attribute_id', $attribute->id);

                if ($minRange && $maxRange) {
                    $munitionQuery->whereBetween('value', [$minRange, $maxRange]);
                } elseif ($minRange) {
                    $munitionQuery->where('value', '>=', $minRange);
                } elseif ($maxRange) {
                    $munitionQuery->where('value', '<=', $maxRange);
                }

                $munitionIds = $munitionQuery->pluck('munition_id');
                $query->whereIn('id', $munitionIds);
            }
        }

        // Apply additional filters
        $query->when($targetType, fn($q) => $q->where('target_type', $targetType))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId));

        $munitions = $query->get();
        $attributes = Cache::remember('attributes_all', 60 * 60, fn() => Attribute::all());

        return view('Frontend.pages.munition_compare', array_merge(
            $this->getCommonViewData(),
            compact('munitions', 'attributes')
        ));
    }

    /**
     * Display the blog page with paginated posts.
     *
     * @return \Illuminate\View\View
     */
    public function blog()
    {
        $posts = Post::where('status', 1)->paginate(6);

        return view('Frontend.pages.blog', array_merge(
            $this->getCommonViewData(),
            compact('posts')
        ));
    }

    /**
     * Display a single blog post by slug.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function blogDetail($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        return view('Frontend.pages.blog_detail', array_merge(
            $this->getCommonViewData(),
            compact('post')
        ));
    }

    /**
     * Display the about page.
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        $munitions = Munition::paginate(9);

        return view('Frontend.pages.about', array_merge(
            $this->getCommonViewData(),
            compact('munitions')
        ));
    }

    /**
     * Display the contact page.
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        $munitions = Munition::paginate(9);

        return view('Frontend.pages.contact', array_merge(
            $this->getCommonViewData(),
            compact('munitions')
        ));
    }

    /**
     * Display a single munition by ID.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $munition = Munition::findOrFail($id);

        return view('Frontend.pages.munition_detail', array_merge(
            $this->getCommonViewData(),
            compact('munition')
        ));
    }

    /**
     * Display munition details by slug.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function ShowMunitionDetail($slug)
    {
        $munition = Munition::where('slug', $slug)->with('images')->firstOrFail();
        $munitionImages = $munition->images;

        return view('Frontend.pages.munition_detail', array_merge(
            $this->getCommonViewData(),
            compact('munition', 'munitionImages')
        ));
    }

    /**
     * Get munitions by category ID, including subcategories.
     *
     * @param int $categoryId
     * @return \Illuminate\Support\Collection
     */
    public function getMunitionsByCategory($categoryId)
    {
        $categoryIds = Category::where('id', $categoryId)
            ->orWhere('parent_id', $categoryId)
            ->pluck('id');

        return Munition::whereIn('category_id', $categoryIds)->get();
    }

    /**
     * Filter munitions by category slug.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function FilterByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $munitions = $this->getMunitionsByCategory($category->id);

        // Paginate the results
        $perPage = 10;
        $currentPage = request()->query('page', 1);
        $pagedData = $munitions->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $munitions = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, $munitions->count(), $perPage);

        $munitionImages = [];
        foreach ($munitions as $munition) {
            $munitionImages[$munition->id] = Image::where('munition_id', $munition->id)->get();
        }

        return view('Frontend.pages.home', array_merge(
            $this->getCommonViewData(),
            compact('munitions', 'munitionImages', 'category')
        ));
    }

    /**
     * Search munitions based on query string.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
        ]);

        $search = $request->input('q');

        $munitions = Munition::where('name', 'like', "%{$search}%")
            ->orWhereHas('category', fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->orWhere('origin', 'like', "%{$search}%")
            ->orWhere('summary', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->paginate(12);

        return view('Frontend.pages.home', array_merge(
            $this->getCommonViewData(),
            compact('munitions', 'search')
        ));
    }
}
