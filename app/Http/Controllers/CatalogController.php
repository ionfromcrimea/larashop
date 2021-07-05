<?php
namespace App\Http\Controllers;

use App\Helpers\ProductFilter;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller {
    public function index() {
//        $roots = Category::where('parent_id', 0)->get();
//        return view('catalog.index', compact('roots'));
        // корневые категории
        $roots = Category::where('parent_id', 0)->get();
        // популярные бренды
        $brands = Brand::popular();
        return view('catalog.index', compact('roots', 'brands'));
    }

//    public function category(Category $category)
    public function category_old(Request $request, Category $category) {

//        $category = Category::where('slug', $slug)->firstOrFail();
//        return view('catalog.category', compact('category'));

        // получаем всех потомков этой категории
//        $descendants = $category->getAllChildren($category->id);
//        $descendants[] = $category->id;
        // товары этой категории и всех потомков
//        $products = Product::whereIn('category_id', $descendants)->paginate(6);
//        return view('catalog.category', compact('category', 'products'));
        $descendants = $category->getAllChildren($category->id);
        $descendants[] = $category->id;
        $builder = Product::whereIn('category_id', $descendants);

        // дешевые или дорогие товары
        if ($request->has('price') && in_array($request->price, ['min', 'max'])) {
            $products = $builder->get();
            $count = $products->count();
            if ($count > 1) {
//                $max = $builder->get()->max('price'); // цена самого дорогого товара
//                $min = $builder->get()->min('price'); // цена самого дешевого товара
//                $avg = ($min + $max) * 0.5;
//                if ($request->price === 'min') {
//                    $builder->where('price', '<=', $avg);
//                } else {
//                    $builder->where('price', '>=', $avg);
//                }

                $half = intdiv($count, 2);
                if ($count % 2) {
                    // нечетное кол-во товаров, надо найти цену товара, который ровно посередине
                    $avg = $products[$half]['price'];
                } else {
                    // четное количество, надо найти такую цену, которая поделит товары пополам
                    // +1 поменял на -1, потому что нумерация начинается с 0
                    $avg = 0.5 * ($products[$half - 1]['price'] + $products[$half]['price']);
                }
                if ($request->price == 'min') {
                    $builder->where('price', '<=', $avg);
                } else {
                    $builder->where('price', '>=', $avg);
                }
            }
//            dd($half, $avg, $products[$half]['price']);
        }
        // отбираем только новинки
        if ($request->has('new')) {
            $builder->where('new', true);
        }
        // отбираем только лидеров продаж
        if ($request->has('hit')) {
            $builder->where('hit', true);
        }
        // отбираем только со скидкой
        if ($request->has('sale')) {
            $builder->where('sale', true);
        }

        $products = $builder->paginate(6)->withQueryString();
        return view('catalog.category', compact('category', 'products'));
    }

    public function brand(Brand $brand) {
//        $brand = Brand::where('slug', $slug)->firstOrFail();
//        return view('catalog.brand', compact('brand'));
        $products = $brand->products()->paginate(6);
        return view('catalog.brand', compact('brand', 'products'));
    }

    public function product(Product $product) {
//        $product = Product::where('slug', $slug)->firstOrFail();
        return view('catalog.product', compact('product'));
    }

    public function category(Request $request, Category $category) {
        $descendants = $category->getAllChildren($category->id);
        $descendants[] = $category->id;
        $builder = Product::whereIn('category_id', $descendants);

        $products = (new ProductFilter($builder, $request))->apply()->paginate(6)->withQueryString();

        return view('catalog.category', compact('category', 'products'));
    }

    public function search(Request $request) {
        $search = $request->input('query');
        $query = Product::search($search);
        $products = $query->paginate(6)->withQueryString();
        return view('catalog.search', compact('products', 'search'));
    }
}
