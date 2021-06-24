<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller {

    /**
     * Показывает список всех профилей
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index() {
        $profiles = auth()->user()->profiles()->paginate(4);
        return view('user.profile.index', compact('profiles'));
    }

    /**
     * Показывает форму для создания профиля
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create() {
        return view('user.profile.create');
    }

    /**
     * Сохраняет новый профиль в базу данных
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        // проверяем данные формы профиля
        $this->validate($request, [
            'user_id' => 'in:' . auth()->user()->id,
            'title' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:255',
            'address' => 'required|max:255',
        ]);
        // валидация пройдена, создаем профиль
        $profile = Profile::create($request->all());
        return redirect()
            ->route('user.profile.show', ['profile' => $profile->id])
            ->with('success', 'Новый профиль успешно создан');
    }

    /**
     * Показывает информацию о профиле
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Profile $profile) {
        if ($profile->user_id !== auth()->user()->id) {
            abort(404); // это чужой профиль
        }
        return view('user.profile.show', compact('profile'));
    }

    /**
     * Показывает форму для редактирования профиля
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Profile $profile) {
        if ($profile->user_id !== auth()->user()->id) {
            abort(404); // это чужой профиль
        }
        return view('user.profile.edit', compact('profile'));
    }

    /**
     * Обновляет профиль (запись в таблице БД)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Profile $profile) {
        // проверяем данные формы профиля
        $this->validate($request, [
            'user_id' => 'in:' . auth()->user()->id,
            'title' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:255',
            'address' => 'required|max:255',
        ]);
        // валидация пройдена, обновляем профиль
        $profile->update($request->all());
        return redirect()
            ->route('user.profile.show', ['profile' => $profile->id])
            ->with('success', 'Профиль был успешно отредактирован');
    }

    /**
     * Удаляет профиль (запись в таблице БД)
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Profile $profile) {
        if ($profile->user_id !== auth()->user()->id) {
            abort(404); // это чужой профиль
        }
        $profile->delete();
        return redirect()
            ->route('user.profile.index')
            ->with('success', 'Профиль был успешно удален');
    }
}
