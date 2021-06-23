<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller {
    /**
     * Показывает список всех страниц
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index() {
        $pages = Page::all();
        return view('admin.page.index', compact('pages'));
    }

    /**
     * Показывает форму для создания страницы
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create() {
        $parents = Page::where('parent_id', 0)->get();
        return view('admin.page.create', compact('parents'));
    }

    /**
     * Сохраняет новую страницу в базу данных
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:100',
            'parent_id' => 'required|regex:~^[0-9]+$~',
            'slug' => 'required|max:100|unique:pages|regex:~^[-_a-z0-9]+$~i',
            'content' => 'required',
        ]);
        $content = $this->saveImages($request->input('content'));
        $data = $request->all();
        $data['content'] = $content;
        $page = Page::create($data);
        return redirect()
            ->route('admin.page.show', ['page' => $page->id])
            ->with('success', 'Новая страница успешно создана');
    }

    /**
     * Показывает информацию о странице сайта
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Page $page) {
        return view('admin.page.show', compact('page'));
    }

    /**
     * Показывает форму для редактирования страницы
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Page $page) {
        $parents = Page::where('parent_id', 0)->get();
        return view('admin.page.edit', compact('page', 'parents'));
    }

    /**
     * Обновляет страницу (запись в таблице БД)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Page $page) {
        $this->validate($request, [
            'name' => 'required|max:100',
            'parent_id' => 'required|regex:~^[0-9]+$~|not_in:'.$page->id,
            'slug' => 'required|max:100|unique:pages,slug,'.$page->id.',id|regex:~^[-_a-z0-9]+$~i',
            'content' => 'required',
        ]);
        $content = $this->saveImages($request->input('content'));
        $data = $request->all();
        $data['content'] = $content;
        $page->update($data);
        return redirect()
            ->route('admin.page.show', ['page' => $page->id])
            ->with('success', 'Страница была успешно отредактирована');
    }

    /**
     * Удаляет изображения, которые связаны со страницей
     *
     * @param  string $content
     * @return void
     */
    private function removeImages($content) {
        $dom = new \DomDocument();
        $dom->loadHtml($content);
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $pattern = '~/storage/page/([0-9a-z]+\.(jpeg|png|gif))~i';
            if (preg_match($pattern, $src, $match)) {
                $name = $match[1];
                if (Storage::disk('public')->exists('page/' . $name)) {
                    Storage::disk('public')->delete('page/' . $name);
                }
            }
        }
    }

    /**
     * Удаляет страницу (запись в таблице БД)
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Page $page) {
        if ($page->children->count()) {
            return back()->withErrors('Нельзя удалить страницу, у которой есть дочерние');
        }
        $this->removeImages($page->content);
        $page->delete();
        return redirect()
            ->route('admin.page.index')
            ->with('success', 'Страница сайта успешно удалена');
    }

    /**
     * Сохраняет на диск изображения и заменяет атрибут src тегов img
     * <img src="data:image/png;base64,R0lGODlhEAAOALDD..." alt="" />
     * <img src="http://server.com/storage/page/123456.png" alt="" />
     *
     * @param $content
     * @return string
     */
    private function saveImages($content) {
        $dom = new \DomDocument('1.0', 'UTF-8');
        // loadHTML() считает, что строка в кодировке ISO-8859-1,
        // поэтому указываем явно, что строка в кодировке UTF-8
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"/></head>';
        $html = $html . '<body>'.$content.'</body></html>';
        $dom->loadHtml($html);
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $data = $img->getAttribute('src');
            if (strpos($data, 'data') === false) {
                continue;
            }
            // <img src="data:image/jpeg;base64,R0lGODlhEAAOAL..." alt="" />
            // data:image/jpeg;base64, data:image/png;base64, data:image/gif
            list($type, $data) = explode(';', $data);
            list(, $ext) = explode('/', $type);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $name = md5(uniqid(rand(), true)) . '.' . $ext;
            Storage::disk('public')->put('page/' . $name, $data);
//            $url = Storage::disk('public')->url('page/' . $name);
            $url = Storage::url('page/' . $name);
            $img->removeAttribute('data-filename');
            $img->removeAttribute('src');
            $img->setAttribute('src', $url);
        }
        $content = html_entity_decode($dom->saveXML($dom->documentElement));
        $content = str_replace(
            [
                '<html><head><meta charset="UTF-8"/></head><body>',
                '</body></html>',
            ],
            '',
            $content
        );
        $content = trim($content);
//        dd($content);
        return $content;
    }
}
