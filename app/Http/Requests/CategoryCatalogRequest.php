<?php

namespace App\Http\Requests;

use App\Rules\CategoryParent;
use Illuminate\Foundation\Http\FormRequest;

class CategoryCatalogRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        switch ($this->method()) {
            case 'POST':
                return [
//                    'parent_id' => 'integer',
                    'parent_id' => 'required|regex:~^[0-9]+$~',
                    'name' => 'required|max:100',
                    'slug' => 'required|max:100|unique:categories,slug|regex:~^[-_a-z0-9]+$~i',
                    'image' => 'mimes:jpeg,jpg,png|max:5000'
                ];
            case 'PUT':
            case 'PATCH':
                // получаем объект модели категории из маршрута: admin/category/{category}
                $model = $this->route('category');
                // из объекта модели получаем уникальный идентификатор для валидации
                $id = $model->id;
                return [
//                    'parent_id' => 'integer',
                    'parent_id' => ['required', 'regex:~^[0-9]+$~', new CategoryParent($model)],
                    'name' => 'required|max:100',
                    /*
                     * Проверка на уникальность slug, исключая эту категорию по идентифкатору:
                     * 1. categories — таблица базы данных, где пороверяется уникальность
                     * 2. slug — имя колонки, уникальность значения которой проверяется
                     * 3. значение, по которому из проверки исключается запись таблицы БД
                     * 4. поле, по которому из проверки исключается запись таблицы БД
                     * Для проверки будет использован такой SQL-запрос к базе данныхЖ
                     * SELECT COUNT(*) FROM `categories` WHERE `slug` = '...' AND `id` <> 17
                     */
                    'slug' => 'required|max:100|unique:categories,slug,'.$id.',id|regex:~^[-_a-z0-9]+$~i',
                    'image' => 'mimes:jpeg,jpg,png|max:5000'
                ];
        }
    }
}
