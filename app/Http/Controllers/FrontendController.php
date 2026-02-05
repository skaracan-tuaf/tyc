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
     * Cache kullanarak performansı optimize eder.
     *
     * @return array
     */
    private function getCommonViewData(): array
    {
        return Cache::remember('frontend.common_data', 3600, function () {
            return [
                'categories' => Category::all(),
                'tags' => Tag::all(),
                'platforms' => Platform::all(),
                'targets' => Target::all(),
                'targetTypes' => Munition::distinct()->pluck('target_type'),
            ];
        });
    }

    /**
     * Display the homepage with paginated and filterable munitions.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Filtre ve sıralama parametrelerini doğrula
        $request->validate([
            'sort' => 'nullable|in:default,price_asc,price_desc,score_desc',
            'target_type' => 'nullable|in:SOFT,HARD',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        $query = Munition::query();

        // Hedef tipine göre filtrele
        if ($request->filled('target_type')) {
            $query->where('target_type', $request->input('target_type'));
        }

        // Kategoriye göre filtrele
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Sıralama
        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'score_desc':
                $query->orderBy('score', 'desc');
                break;
            case 'default':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Ana sayfada mühimmatları sayfalama ile getir
        // withQueryString ile mevcut filtre / arama parametrelerini sayfalama linklerine ekler
        $munitions = $query
            ->paginate(12)
            ->withQueryString();

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

        // Statik veri - kategori, hedef adı ve meteorolojik duruma göre sonuçlar
        $staticData = [
            'Hava Hava Füze' => [
                'Yüksek Manevra Kabiliyetli Muharip Uçaklar' => [ 
                    'Açık' => [
                        ['name' => 'Gökbora',              'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'Bozdoğan',             'rank' => 5, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 6, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 7, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Sisli' => [
                        ['name' => 'Gökbora',              'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 5, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'Gökbora',              'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 5, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                ],
                'Yüksek Değerli Stratejik Hedefler' => [ 
                    'Açık' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'Bozdoğan',             'rank' => 5, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 7, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Sisli' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 5, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 6, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 5, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 6, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                ],
                'Yakın Temas (Dogfight) Tipi Hava Hedefi' => [ 
                    'Açık' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'Gökdoğan',             'rank' => 3, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 4, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 5, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 6, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 7, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Sisli' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'Gökdoğan',             'rank' => 3, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 4, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 5, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'Gökdoğan',             'rank' => 3, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 4, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 5, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                ],
                'Düşük Görünürlüklü (Stealth) Hedefler' => [ 
                    'Açık' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'Bozdoğan',             'rank' => 5, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 6, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 7, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Sisli' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 5, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 5, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                ],
                'Düşük Hızlı TİHA veya Helikopter Hedefleri' => [
                    'Açık' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'Bozdoğan',             'rank' => 5, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 6, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 7, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Sisli' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 5, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                    'Yağmurlu' => [
                        ['name' => 'Gökbora',     'rank' => 1, 'cost' => '~$1,000,000',        'platform' => ['F-16']],
                        ['name' => 'Gökhan',               'rank' => 2, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                        ['name' => 'AIM-120C-7 AMRAAM',    'rank' => 3, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Gökdoğan',             'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9X Sidewinder',    'rank' => 5, 'cost' => '$472,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'EA-18G', 'Eurofighter Typhoon']],
                        ['name' => 'AIM-120B AMRAAM',      'rank' => 6, 'cost' => '~$386,000',          'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                        ['name' => 'Bozdoğan',             'rank' => 7, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                        ['name' => 'AIM-9M Sidewinder',    'rank' => 8, 'cost' => '$400,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'A-10', 'F-4']],
                    ],
                ],
                '10 NM Uzaklıktaki F-35 Hedefi' => [
                    'Açık' => [
                    ['name' => 'Gökhan',                'rank' => 1, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                    ['name' => 'Gökbora',               'rank' => 3, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                    ['name' => 'Gökdoğan',              'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                    ['name' => 'Bozdoğan',              'rank' => 6, 'cost' => '~$1,000,000',        'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                    ['name' => 'AIM-120C-7 AMRAAM',     'rank' => 2, 'cost' => '$386,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                    ['name' => 'AIM-120B AMRAAM',       'rank' => 7, 'cost' => '$386,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                    ],
                ],
                '30 NM Uzaklıktaki F-35 Hedefi' => [
                    'Açık' => [
                    ['name' => 'AIM-120C-7 AMRAAM',     'rank' => 2, 'cost' => '$386,000',           'platform' => ['F-15', 'F-16', 'F/A-18', 'F-22', 'F-35', 'Eurofighter Typhoon', 'F-4']],
                    ['name' => 'Gökbora ',      'rank' => 3, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                    ['name' => 'Gökhan',                'rank' => 1, 'cost' => '~$1,200,000',        'platform' => ['F-16']],
                    ['name' => 'Gökdoğan',              'rank' => 4, 'cost' => '~$600,000',          'platform' => ['F-16', 'Hürjet', 'Akıncı', 'F-4E 2020']],
                    ],
                ],
                        ],
                        'Hava Yer Füze' => [
                            'Rıhtım / İskele' => [ 
                                'Açık' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B1',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 4,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'Gezgin',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 6,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 7,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-B2',                        'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'SOM-J',                         'rank' => 12, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B1',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'Gezgin',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 5,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 6,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-B2',                        'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 10, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B1',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'Gezgin',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 5,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 6,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-B2',                        'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 10, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],
                            'Ana Pist' => [
                                'Açık' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 3,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 4,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 6,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 8,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 9,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 12, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 3,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 4,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 6,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 8,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 3,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 4,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 6,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 8,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],
                            'Uçak Bakım Hangarı' => [ 
                                'Açık' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 3,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 4,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B2',                        'rank' => 7,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 8,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 9,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 12, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 3,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 4,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B2',                        'rank' => 7,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 3,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 4,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B2',                        'rank' => 7,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],
                            'Savaş Harekât Merkezi' => [ 
                                'Açık' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 3,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 4,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'Akbaba',                        'rank' => 7,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 8,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 9,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 12, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 3,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 4,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'Akbaba',                        'rank' => 7,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 8,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 3,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 4,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'Akbaba',                        'rank' => 7,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 8,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],
                            'Tam Korumalı Mühimmat Deposu' => [ 
                                'Açık' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B2',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'Gezgin',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 7,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'Akbaba',                        'rank' => 8,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 9,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'SOM-J',                         'rank' => 12, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B2',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'Gezgin',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'Akbaba',                        'rank' => 7,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 8,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 10, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B2',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'Gezgin',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'Akbaba',                        'rank' => 7,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 8,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 10, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],
                            'Radar Mevzii - Radar Anteni' => [ 
                                'Açık' => [
                                    ['name' => 'Akbaba',                        'rank' => 1,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 2,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 3,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-B2',                        'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 9,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 12, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'Akbaba',                        'rank' => 1,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 2,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 3,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-B2',                        'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'Akbaba',                        'rank' => 1,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 2,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 3,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-B2',                        'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],
                            'Mağara' => [ 
                                'Açık' => [
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 1,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 2,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 3,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B2',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 5,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'SOM-B1',                        'rank' => 6,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 7,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 8,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'Gezgin',                        'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'SOM-J',                         'rank' => 12, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 1,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 2,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 3,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B2',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 6,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'Akbaba',                        'rank' => 7,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'Gezgin',                        'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 9,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'SOM-J',                         'rank' => 12, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B2',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 4,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-B1',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Gezgin',                        'rank' => 6,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'SOM-A',                         'rank' => 7,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 8,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 9,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 11, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'Çakır',                         'rank' => 12, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                            ],
                            'Türbin / Jeneratör Bölümü' => [ 
                                'Açık' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 5,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'SOM-B2',                        'rank' => 6,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 7,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 9,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 12, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 5,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'SOM-B2',                        'rank' => 6,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 7,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 3,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 5,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'SOM-B2',                        'rank' => 6,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 7,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],
                            'Radar Mevzii - Komuta Kontrol / Muhabere Veni' => [ 
                                'Açık' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 3,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 5,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-B2',                        'rank' => 6,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 9,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'SOM-A',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 12, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 3,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 5,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-B2',                        'rank' => 6,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 1,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'Gezgin',                        'rank' => 2,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'Akbaba',                        'rank' => 3,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 5,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'SOM-B2',                        'rank' => 6,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 8,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'SOM-A',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],
                            'Radar Mevzii - Muhabere Anteni' => [ 
                                'Açık' => [
                                    ['name' => 'Akbaba',                        'rank' => 1,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 2,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 3,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Gezgin',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-A',                         'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 9,  'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                    ['name' => 'SOM-J',                         'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 11, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 12, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 13, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                ],
                                'Sisli' => [
                                    ['name' => 'Akbaba',                        'rank' => 1,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 2,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 3,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Gezgin',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-A',                         'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                                'Yağmurlu' => [
                                    ['name' => 'Akbaba',                        'rank' => 1,  'cost' => '~$300,000',          'platform' => ['F-16', 'KAAN', 'Akıncı', 'Aksungur', 'Anka-3']],
                                    ['name' => 'AGM-88 HARM',                   'rank' => 2,  'cost' => '$287,000–$312,000',  'platform' => ['F-4G', 'F-16C', 'F/A-18', 'EA-18G']],
                                    ['name' => 'AGM-84H SLAM-ER',               'rank' => 3,  'cost' => '$400,000–$655,000',  'platform' => ['F/A-18', 'P-3 Orion', 'S-3 Viking']],
                                    ['name' => 'SOM-B1',                        'rank' => 4,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Gezgin',                        'rank' => 5,  'cost' => '~$1,000,000',        'platform' => ['F-4E/2020', 'F-16']],
                                    ['name' => 'AGM-142 Have Nap (Popeye I)',   'rank' => 6,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'B-52H']],
                                    ['name' => 'AGM-142 Have Lite (Popeye II)', 'rank' => 7,  'cost' => '$500,000–$800,000',  'platform' => ['F-4E 2020', 'F-16C']],
                                    ['name' => 'SOM-A',                         'rank' => 8,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-J',                         'rank' => 9,  'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'SOM-B2',                        'rank' => 10, 'cost' => '~$1,000,000',        'platform' => ['F-4E 2020', 'F-16']],
                                    ['name' => 'Çakır',                         'rank' => 11, 'cost' => '~$500,000',          'platform' => ['SİHA', 'Muharip Uçak', 'Sabit Kanatlı Hava Platformları']],
                                    ['name' => 'Kemankeş',                      'rank' => 12, 'cost' => '~$20,000–$50,000',   'platform' => ['Akıncı TİHA', 'Bayraktar TB2-TB3 SİHA']],
                                    ['name' => 'AGM-65E Maverick',              'rank' => 13, 'cost' => '$110,000–$158,000',  'platform' => ['F-16', 'A-10', 'F-15E']],
                                ],
                            ],                    
                        ],
                        'Hava Yer Bomba' => [
                'Rıhtım / İskele' => [
                'Açık' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'MAM-T',                         'rank' => 6,  'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'HGK-84',                        'rank' => 7,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN',                         'rank' => 8,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 9,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83',                        'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 14, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 15, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'TEBER-81',                      'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 17, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 19, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 20, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-82',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L',                         'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-C',                         'rank' => 24, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                         'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN',                         'rank' => 7,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83',                        'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 11, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TEBER-81',                      'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 13, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 15, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L',                         'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-T',                         'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-C',                         'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                         'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN',                         'rank' => 7,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83',                        'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 11, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TEBER-81',                      'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 13, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 15, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L',                         'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-T',                         'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-C',                         'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                         'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                'Ana Pist' => [
                'Açık' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 2,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 4,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 5,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'KGK-82',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 7,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83',                        'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-T',                         'rank' => 10, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 11, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 12, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'TOLUN',                         'rank' => 13, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 14, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'TEBER-81',                      'rank' => 19, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 21, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-C',                         'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-L',                         'rank' => 25, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'Bozok',                         'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 2,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 4,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 5,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'KGK-82',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83',                        'rank' => 7,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 8,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TOLUN',                         'rank' => 9,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 10, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'TEBER-81',                      'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 15, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L',                         'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 20, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-T',                         'rank' => 22, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'MAM-C',                         'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                         'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 2,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 4,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 5,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'KGK-82',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83',                        'rank' => 7,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 8,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TOLUN',                         'rank' => 9,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 10, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'TEBER-81',                      'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 15, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L',                         'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 20, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-T',                         'rank' => 22, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'MAM-C',                         'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                         'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                'Uçak Bakım Hangarı' => [
                'Açık' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-T',                         'rank' => 7,  'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'TOLUN',                         'rank' => 8,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 9,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83',                        'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 12, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 15, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'TEBER-81',                      'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 17, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 19, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L',                         'rank' => 22, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-C',                         'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok',                         'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN',                         'rank' => 7,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83',                        'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 9,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'TEBER-81',                      'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 13, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L',                         'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-T',                         'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'MAM-C',                         'rank' => 25, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 26, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok',                         'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN',                         'rank' => 7,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83',                        'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 9,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'TEBER-81',                      'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 13, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L',                         'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-T',                         'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'MAM-C',                         'rank' => 25, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 26, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok',                         'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                'Savaş Harekât Merkezi' => [
                'Açık' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'MAM-T',                         'rank' => 5,  'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'TOLUN',                         'rank' => 6,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 7,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-84',                        'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-83',                        'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 11, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 16, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TEBER-81',                      'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 18, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 19, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 21, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-C',                         'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-L',                         'rank' => 24, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                         'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'TOLUN',                         'rank' => 5,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 6,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 7,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-84',                        'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-83',                        'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 12, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TEBER-81',                      'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 14, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 15, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L',                         'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-T',                         'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 23, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 24, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'MAM-C',                         'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                         'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'TOLUN',                         'rank' => 5,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 6,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 7,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-84',                        'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-83',                        'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 12, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TEBER-81',                      'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 14, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 15, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L',                         'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-T',                         'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 23, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 24, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'MAM-C',                         'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-82',                        'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                         'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                'Tam Korumalı Mühimmat Deposu' => [
                'Açık' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 3,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 4,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 5,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 7,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                        'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 9,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-83',                        'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 11, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 12, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-T',                         'rank' => 15, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'TEBER-81',                      'rank' => 19, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 20, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'LGK-82',                        'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'TOLUN',                         'rank' => 23, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'MAM-C',                         'rank' => 24, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-L',                         'rank' => 25, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'Bozok',                         'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-84 NEB', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe', 'rank' => 3, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 4, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 5, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 6, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-83', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'SZ-109 NEB', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 9, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'TEBER-81', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN', 'rank' => 16, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'MAM-L', 'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'LGK-84 NEB', 'rank' => 19, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 20, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84', 'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'MAM-T', 'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-82', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 26, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-C', 'rank' => 27, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-84 NEB', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe', 'rank' => 3, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 4, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 5, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 6, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-83', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'SZ-109 NEB', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 9, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'TEBER-81', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN', 'rank' => 16, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'MAM-L', 'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'LGK-84 NEB', 'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'MAM-T', 'rank' => 24, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-82', 'rank' => 26, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-C', 'rank' => 27, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                'Radar Mevzii - Radar Anteni' => [
                'Açık' => [
                    ['name' => 'KGK-83',                        'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',                'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                        'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                         'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)',           'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                        'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',     'rank' => 7,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'MAM-T',                         'rank' => 8,  'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'TOLUN',                         'rank' => 9,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'LGK-84',                        'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83',                        'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',         'rank' => 12, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)',                 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',                 'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)',   'rank' => 15, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'TEBER-81',                      'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                    'rank' => 17, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-12 Paveway II',             'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'GBU-38 JDAM',                   'rank' => 19, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                        'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'LGK-82',                        'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-L',                         'rank' => 22, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-C',                         'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'HGK-84 NEB',                    'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-84 NEB',                    'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok',                         'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                  'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon',   'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82', 'rank' => 3, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe', 'rank' => 4, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 5, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 6, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'TEBER-81', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L', 'rank' => 16, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'HGK-84 NEB', 'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 19, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'MAM-T', 'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'LGK-84', 'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 22, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-82', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-C', 'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-84 NEB', 'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82', 'rank' => 3, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe', 'rank' => 4, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 5, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 6, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'TEBER-81', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L', 'rank' => 16, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'HGK-84 NEB', 'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 20, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'MAM-T', 'rank' => 21, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'LGK-84', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-82', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-C', 'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-84 NEB', 'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                'Mağara' => [
                    'Açık' => [
                    ['name' => 'LGK-84 NEB',                  'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',   'rank' => 2,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                      'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-84 NEB',                  'rank' => 4,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 5,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'SZ-109 NEB',                  'rank' => 6,  'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'Gökçe (MK-83)',               'rank' => 7,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'GBU-31 JDAM (Mk 84)',         'rank' => 8,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                      'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',       'rank' => 10, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-83',                      'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'KGK-83',                      'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',               'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',               'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-12 Paveway II',           'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-82',                      'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-T',                       'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'TEBER-81',                    'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'HGK-82',                      'rank' => 19, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-38 JDAM',                 'rank' => 20, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'MAM-C',                       'rank' => 21, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'KGK-82',                      'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L',                       'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'TOLUN',                       'rank' => 24, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'AGM-154 JSOW A',              'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'Bozok',                       'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ],
                    'Sisli' => [
                    ['name' => 'HGK-84 NEB', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 2, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 3, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 4, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'Gökçe (MK-83)', 'rank' => 5, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'LGK-84 NEB', 'rank' => 6, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'KGK-83', 'rank' => 9, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'SZ-109 NEB', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 16, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'KGK-82', 'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'TEBER-81', 'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 19, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-82', 'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-C', 'rank' => 21, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-T', 'rank' => 22, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'TOLUN', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'MAM-L', 'rank' => 24, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'Bozok', 'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ],
                    'Yağmurlu' => [
                    ['name' => 'HGK-84 NEB', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 2, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 3, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 4, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'Gökçe (MK-83)', 'rank' => 5, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'KGK-83', 'rank' => 6, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-84 NEB', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'SZ-109 NEB', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 16, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'KGK-82', 'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'TEBER-81', 'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'MAM-C', 'rank' => 19, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'TOLUN', 'rank' => 20, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'MAM-L', 'rank' => 21, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'LGK-82', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-T', 'rank' => 25, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'Bozok', 'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ],
                ],
                'Türbin / Jeneratör Bölümü' => [
                'Açık' => [
                    ['name' => 'KGK-83',                      'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',              'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'Gökçe',                       'rank' => 3,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'KGK-82',                      'rank' => 4,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (MK 84)',         'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                      'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',   'rank' => 7,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'MAM-T',                       'rank' => 8,  'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'LGK-84',                      'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83',                      'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'TOLUN',                       'rank' => 11, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',       'rank' => 12, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)',               'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',               'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'SZ-109 NEB',                  'rank' => 16, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'TEBER-81',                    'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-12 Paveway II',           'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'GBU-38 JDAM',                 'rank' => 19, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                      'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB',                  'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L',                       'rank' => 22, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-C',                       'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-84 NEB',                  'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-82',                      'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok',                       'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'Gökçe', 'rank' => 3, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'KGK-82', 'rank' => 4, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 5, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 6, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-83', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'TOLUN', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'TEBER-81', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB', 'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L', 'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 20, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'MAM-T', 'rank' => 21, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'LGK-84', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'MAM-C', 'rank' => 25, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-84 NEB', 'rank' => 26, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-82', 'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'Gökçe', 'rank' => 3, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'KGK-82', 'rank' => 4, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 5, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 6, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-83', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'TOLUN', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'TEBER-81', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB', 'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L', 'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 20, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'MAM-T', 'rank' => 21, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'LGK-84', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'MAM-C', 'rank' => 25, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-84 NEB', 'rank' => 26, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-82', 'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                'Radar Mevzii - Komuta Kontrol / Muhabere Veni' => [
                'Açık' => [
                    ['name' => 'KGK-83',                      'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',              'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'Gökçe',                       'rank' => 3,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'KGK-82',                      'rank' => 4,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (MK 84)',         'rank' => 5,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                      'rank' => 6,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-T',                       'rank' => 7,  'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',   'rank' => 8,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                      'rank' => 9,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'TOLUN',                       'rank' => 10, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83',                      'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',       'rank' => 12, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GÖZDE (MK 82)',               'rank' => 14, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',               'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB',                  'rank' => 16, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'TEBER-81',                    'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-12 Paveway II',           'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'GBU-38 JDAM',                 'rank' => 19, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                      'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB',                  'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-84 NEB',                  'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-82',                      'rank' => 23, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-L',                       'rank' => 24, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-C',                       'rank' => 25, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'Bozok',                       'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'Gökçe', 'rank' => 3, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'KGK-82', 'rank' => 4, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 5, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 6, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'TEBER-81', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB', 'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L', 'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'MAM-T', 'rank' => 19, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 20, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84', 'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 22, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-84 NEB', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-82', 'rank' => 26, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-C', 'rank' => 27, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'Gökçe', 'rank' => 3, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'KGK-82', 'rank' => 4, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 5, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 6, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'TOLUN', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'HGK-83', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'TEBER-81', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-84 NEB', 'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'MAM-L', 'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-T', 'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-84 NEB', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-82', 'rank' => 26, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-C', 'rank' => 27, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                'Radar Mevzii - Muhabere Anteni' => [
                'Açık' => [
                    ['name' => 'KGK-83',                      'rank' => 1,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A',              'rank' => 2,  'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82',                      'rank' => 3,  'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe',                       'rank' => 4,  'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'TOLUN',                       'rank' => 5,  'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'MAM-T',                       'rank' => 6,  'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-31 JDAM (MK 84)',         'rank' => 7,  'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84',                      'rank' => 8,  'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'GBU-10 Paveway II (MK 84)',   'rank' => 9,  'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84',                      'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'HGK-83',                      'rank' => 11, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)',               'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)',               'rank' => 13, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)',       'rank' => 14, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TEBER-81',                    'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 16, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'SZ-109 NEB',                  'rank' => 17, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-12 Paveway II',           'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'GBU-38 JDAM',                 'rank' => 19, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82',                      'rank' => 20, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'LGK-82',                      'rank' => 21, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-L',                       'rank' => 22, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'MAM-C',                       'rank' => 23, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'HGK-84 NEB',                  'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'LGK-84 NEB',                  'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok',                       'rank' => 26, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                    ['name' => 'CBU-103 WCMD',                'rank' => 27, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 28, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                ],
                'Sisli' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82', 'rank' => 3, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe', 'rank' => 4, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'TOLUN', 'rank' => 5, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 6, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-83', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TEBER-81', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'SZ-109 NEB', 'rank' => 14, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 15, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 16, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L', 'rank' => 17, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'HGK-84 NEB', 'rank' => 18, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'MAM-T', 'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 21, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-82', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-C', 'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-84 NEB', 'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                'Yağmurlu' => [
                    ['name' => 'KGK-83', 'rank' => 1, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'AGM-154 JSOW A', 'rank' => 2, 'cost' => '-', 'platform' => ['F-16', 'F/A-18', 'B-52']],
                    ['name' => 'KGK-82', 'rank' => 3, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Gökçe', 'rank' => 4, 'cost' => '-', 'platform' => ['AKINCI SİHA ve F-16 PO-III uyumlu']],
                    ['name' => 'TOLUN', 'rank' => 5, 'cost' => '-', 'platform' => ['F-16 Blok 40', 'Akıncı']],
                    ['name' => 'GBU-31 JDAM (MK 84)', 'rank' => 6, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'HGK-84', 'rank' => 7, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'HGK-83', 'rank' => 8, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GÖZDE (MK 82)', 'rank' => 9, 'cost' => '-', 'platform' => ['F-16', 'F-4E/ 2020', 'Akıncı']],
                    ['name' => 'TEBER (MK 82)', 'rank' => 10, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'GBU-31 JDAM (BLU-109)', 'rank' => 11, 'cost' => '-', 'platform' => ['F-15E','F-16','F-22','B-1B','B-2','B-52','MQ-9']],
                    ['name' => 'TEBER-81', 'rank' => 12, 'cost' => '-', 'platform' => ['F-16', 'AT-802', 'C295']],
                    ['name' => 'SZ-109 NEB', 'rank' => 13, 'cost' => '-', 'platform' => ['F-16']],
                    ['name' => 'GBU-38 JDAM', 'rank' => 14, 'cost' => '-', 'platform' => ['F-15E', 'F-16', 'B-52']],
                    ['name' => 'HGK-82', 'rank' => 15, 'cost' => '-', 'platform' => ['F-16', 'F-4 2020']],
                    ['name' => 'MAM-L', 'rank' => 16, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'HGK-84 NEB', 'rank' => 17, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'CBU-103 WCMD', 'rank' => 18, 'cost' => '-', 'platform' => ['F-15', 'F-16']],
                    ['name' => 'CBU-105 Sensor Fuzed Weapon', 'rank' => 19, 'cost' => '-', 'platform' => ['F-15', 'B-52']],
                    ['name' => 'MAM-T', 'rank' => 20, 'cost' => '-', 'platform' => ['Bayraktar TB2','Anka-S','Aksungur']],
                    ['name' => 'GBU-10 Paveway II (MK 84)', 'rank' => 21, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'LGK-84', 'rank' => 22, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'GBU-10 Paveway II (BLU-109)', 'rank' => 23, 'cost' => '-', 'platform' => ['F-16','F-15','A-10','B-1B','B-52H']],
                    ['name' => 'GBU-12 Paveway II', 'rank' => 24, 'cost' => '-', 'platform' => ['F-16', 'A-10', 'B-52', 'F/A-18']],
                    ['name' => 'LGK-82', 'rank' => 25, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020', 'Akıncı']],
                    ['name' => 'MAM-C', 'rank' => 26, 'cost' => '-', 'platform' => ['Bayraktar TB2', 'Anka-S', 'Aksungur']],
                    ['name' => 'LGK-84 NEB', 'rank' => 27, 'cost' => '-', 'platform' => ['F-16', 'F-4E 2020']],
                    ['name' => 'Bozok', 'rank' => 28, 'cost' => '-', 'platform' => ['TB2 SİHA', 'Akıncı', 'Aksungur']],
                ],
                ],
                        ],
                    ];
            

        // Seçilen kategori, hedef adı ve meteorolojik duruma göre sonuçları al
        $results = $staticData[$categoryName][$targetName][$weather] ?? [];

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
        // Filtreleme için gelen parametreleri doğrula
        $request->validate([
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:munitions,id',
            'target_type' => 'nullable|string|max:255',
            'min' => 'nullable|numeric|min:0',
            'max' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $selectedIds = $request->input('ids', []);

        // Eğer belirli mühimmat id'leri seçildiyse, yalnızca bunları kıyasla
        if (is_array($selectedIds) && count($selectedIds) > 0) {
            $munitions = Munition::whereIn('id', $selectedIds)->get();

            // Attribute'ları cache'den al (1 saat)
            $attributes = Cache::remember('attributes_all', 3600, fn() => Attribute::all());

            return view('Frontend.pages.munition_compare', array_merge(
                $this->getCommonViewData(),
                compact('munitions', 'attributes')
            ));
        }

        $targetType = $request->input('target_type');
        $minRange = $request->input('min');
        $maxRange = $request->input('max');
        $categoryId = $request->input('category_id');

        // Hiç filtre yoksa, boş koleksiyon dön ve sadece kıyaslama ekranını göster
        if (!$targetType && !$minRange && !$maxRange && !$categoryId) {
            $munitions = collect();

            // Attribute'ları cache'den al (1 saat)
            $attributes = Cache::remember('attributes_all', 3600, fn() => Attribute::all());

            return view('Frontend.pages.munition_compare', array_merge(
                $this->getCommonViewData(),
                compact('munitions', 'attributes')
            ));
        }

        $query = Munition::query();

        // Menzil filtresi uygulama (varsa)
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

        // Ek filtreleri uygula (hedef tipi ve kategori)
        $query->when($targetType, fn($q) => $q->where('target_type', $targetType))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId));

        // Kıyaslama için mühimmatları al
        $munitions = $query->get();

        // Attribute'ları cache'den al (1 saat)
        $attributes = Cache::remember('attributes_all', 3600, fn() => Attribute::all());

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
        // Aktif blog yazılarını sayfalama ile getir
        $posts = Post::where('status', 1)->orderBy('created_at', 'desc')->paginate(6);

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
    public function blogDetail(string $slug)
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
        // Hakkımızda sayfasında mühimmatları getir
        $munitions = Munition::orderBy('created_at', 'desc')->paginate(9);

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
        // İletişim sayfasında mühimmatları getir
        $munitions = Munition::orderBy('created_at', 'desc')->paginate(9);

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
    public function show(int $id)
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
    public function ShowMunitionDetail(string $slug)
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getMunitionsByCategory(int $categoryId): Collection
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
    public function FilterByCategory(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $allMunitions = $this->getMunitionsByCategory($category->id);

        // Paginate the results
        $perPage = 10;
        $currentPage = request()->query('page', 1);
        $pagedData = $allMunitions->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $munitions = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $allMunitions->count(),
            $perPage,
            $currentPage,
            [
                // Sayfalama linkleri kategori sayfasında kalsın
                'path' => route('kategoriFiltresi', $slug),
                // Diğer query parametreleri (varsa) korunsun
                'query' => request()->except('page'),
            ]
        );

        // Mühimmat görsellerini toplu olarak yükle (N+1 sorgu problemini önler)
        $munitionIds = $munitions->pluck('id');
        $images = Image::whereIn('munition_id', $munitionIds)->get()->groupBy('munition_id');
        $munitionImages = [];
        foreach ($munitionIds as $id) {
            $munitionImages[$id] = $images->get($id, collect());
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

        // Arama sorgusu - çoklu alan araması
        $munitions = Munition::where('name', 'like', "%{$search}%")
            ->orWhereHas('category', fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->orWhere('origin', 'like', "%{$search}%")
            ->orWhere('summary', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('Frontend.pages.home', array_merge(
            $this->getCommonViewData(),
            compact('munitions', 'search')
        ));
    }
}
