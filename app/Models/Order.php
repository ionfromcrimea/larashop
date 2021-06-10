<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'comment',
        'amount',
    ];

    /**
     * Связь «один ко многим» таблицы `orders` с таблицей `order_items`
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
