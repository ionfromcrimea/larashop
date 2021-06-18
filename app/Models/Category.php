<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Category[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @mixin \Eloquent
 */
class Category extends Model {

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'content',
        'image',
    ];

    /**
     * Связь «один ко многим» таблицы `categories` с таблицей `products`
     */
    public function products() {
        return $this->hasMany(Product::class);
    }

    /**
     * Возвращает список корневых категорий каталога товаров
     */
    public static function roots() {
        return self::where('parent_id', 0)->with('children')->get();
    }

    /**
     * Связь «один ко многим» таблицы `categories` с таблицей `categories`
     */
    public function children() {
//        $q = $this->hasMany(Category::class, 'parent_id');
//        dd($q->modelKeys());
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Проверяет, что переданный идентификатор id НЕ может быть родителем
     * этой категории (ЭТОГО ОБЪЕКТА КАТЕГОРИИ); что категорию не пытаются поместить внутрь себя
     */
    public function validParent($id) {
        $id = (integer)$id;
        // получаем идентификаторы всех потомков текущей категории
        $ids = $this->getAllChildren($this->id);
        $ids[] = $this->id;
        return ! in_array($id, $ids);
    }

    /**
     * Возвращает всех потомков категории с идентификатором $id
     */
    public function getAllChildren($id) {
        // получаем прямых потомков категории с идентификатором $id
        $children = self::where('parent_id', $id)->with('children')->get();
        $ids = [];
        foreach ($children as $child) {
            $ids[] = $child->id;
            // для каждого прямого потомка получаем его прямых потомков
            if ($child->children->count()) {
                $ids = array_merge($ids, $this->getAllChildren($child->id));
            }
        }
        return $ids;
    }

    /**
     * Связь «один ко многим» таблицы `categories` с таблицей `categories`, но
     * позволяет получить не только дочерние категории, но и дочерние-дочерние
     */
    public function descendants() {
        return $this->hasMany(Category::class, 'parent_id')->with('descendants');
    }

    /**
     * Возвращает список всех категорий каталога в виде дерева
     */
    public static function hierarchy() {
        return self::where('parent_id', 0)->with('descendants')->get();
    }
}
