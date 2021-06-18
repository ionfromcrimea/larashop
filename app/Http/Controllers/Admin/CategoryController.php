<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageSaver;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CategoryCatalogRequest;

class CategoryController extends Controller
{
    private $imageSaver;

    public function __construct(ImageSaver $imageSaver)
    {
        $this->imageSaver = $imageSaver;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
//        $roots = Category::roots();
//        return view('admin.category.index', compact('roots'));
        $items = Category::all();
        return view('admin.category.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        // для возможности выбора родителя
//        $parents = Category::roots();
//        return view('admin.category.create', compact('parents'));
        // все категории для возможности выбора родителя
        $items = Category::all();
        return view('admin.category.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(CategoryCatalogRequest $request)
    {
        /*
         * Проверяем данные формы создания категории
         */
//        $this->validate($request, [
//            'parent_id' => 'integer',
//            'name' => 'required|max:100',
//            'slug' => 'required|max:100|unique:categories,slug|regex:~^[-_a-z0-9]+$~i',
//            'image' => 'mimes:jpeg,jpg,png|max:5000'
//        ]);
        /*
         * Проверка пройдена, создаем категорию
         */
//        $file = $request->file('image');
//        if ($file) { // был загружен файл изображения
//            $path = $file->store('catalog/category/source', 'public');
//            $base = basename($path);
//        }
        $data = $request->all();
        $data['image'] = $this->imageSaver->upload($request, null, 'category');
        $category = Category::create($data);
        return redirect()
            ->route('admin.category.show', ['category' => $category->id])
            ->with('success', 'Новая категория успешно создана');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Category $category)
    {
        return view('admin.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Category $category)
    {
        // для возможности выбора родителя
//        $parents = Category::roots();
//        return view('admin.category.edit', compact('category', 'parents'));
        // все категории для возможности выбора родителя
        $items = Category::all();
        return view('admin.category.create', compact('items'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(CategoryCatalogRequest $request, Category $category)
    {
        /*
         * Проверяем данные формы редактирования категории
         */
//        dd($request);
//        $id = $category->id;
//        $this->validate($request, [
//            'parent_id' => 'integer',
//            'name' => 'required|max:100',
            /*
             * Проверка на уникальность slug, исключая эту категорию по идентифкатору:
             * 1. categories — таблица базы данных, где пороверяется уникальность
             * 2. slug — имя колонки, уникальность значения которой проверяется
             * 3. значение, по которому из проверки исключается запись таблицы БД
             * 4. поле, по которому из проверки исключается запись таблицы БД
             * Для проверки будет использован такой SQL-запрос к базе данныхЖ
             * SELECT COUNT(*) FROM `categories` WHERE `slug` = '...' AND `id` <> 17
             */
//            'slug' => 'required|max:100|unique:categories,slug,' . $id . ',id|regex:~^[-_a-z0-9]+$~i',
//            'image' => 'mimes:jpeg,jpg,png|max:5000'
//        ]);
        /*
         * Проверка пройдена, обновляем категорию
         */
//        if ($request->remove) { // если надо удалить изображение
//            $old = $category->image;
//            if ($old) {
//                Storage::disk('public')->delete('catalog/category/source/' . $old);
//            }
//            $file = null;
//        } else $file = $request->file('image');
////        dd($file);
//        if ($file) { // был загружен файл изображения (или имелся перед редактированием)
//            $path = $file->store('catalog/category/source', 'public');
//            $base = basename($path);
//            // удаляем старый файл изображения
//            $old = $category->image;
//            if ($old) {
//                Storage::disk('public')->delete('catalog/category/source/' . $old);
//            }
//        }
        $data = $request->all();
        $data['image'] = $this->imageSaver->upload($request, $category, 'category');
        $category->update($data);
        return redirect()
            ->route('admin.category.show', ['category' => $category->id])
            ->with('success', 'Категория была успешно исправлена');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
//        var_dump($category->children->count());
        if ($category->children->count()) {
            $errors[] = 'Нельзя удалить категорию с дочерними категориями';
        }
        if ($category->products->count()) {
            $errors[] = 'Нельзя удалить категорию, которая содержит товары';
        }
//        dd($errors);
        if (!empty($errors)) {
            return back()->withErrors($errors);
        }
//        dd($category);
        $category->delete();
        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Категория каталога успешно удалена');
    }
}
