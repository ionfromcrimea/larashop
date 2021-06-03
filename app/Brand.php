<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model {
    /**
     * Связь «один ко многим» таблицы `brands` с таблицей `products`
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products() {
        return $this->hasMany(Product::class);
    }
}
