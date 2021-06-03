<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    /**
     * Связь «один ко многим» таблицы `categories` с таблицей `products`
     */
    public function products() {
        return $this->hasMany(Product::class);
    }
}
