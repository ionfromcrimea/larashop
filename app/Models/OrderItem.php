<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /**
     * Связь «элемент принадлежит» таблицы `order_items` с таблицей `products`
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
