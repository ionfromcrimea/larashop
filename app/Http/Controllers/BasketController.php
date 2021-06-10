<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class BasketController extends Controller
{

    private $basket;
//    public static $order;

    public function __construct()
    {
//        $this->getBasket();
        $this->basket = Basket::getBasket();
    }

    /**
     * Показывает корзину покупателя
     */
    public function index(Request $request)
    {
        $basket_id = $request->cookie('basket_id');
        if (!empty($basket_id)) {
            $products = Basket::findOrFail($basket_id)->products;
            return view('basket.index', compact('products'));
        } else {
            abort(404);
        }
    }

    /**
     * Форма оформления заказа
     */
    public function checkout()
    {
        if (Basket::getCount() == 0) {
//            dd(Basket::getCount());
            return redirect()->route('basket.index')->with('success', 'Ваша корзина пуста');
        }
        return view('basket.checkout');
    }

    /**
     * Добавляет товар с идентификатором $id в корзину
     */
    public function add(Request $request, $id)
    {
        $quantity = $request->input('quantity') ?? 1;
        $this->basket->increase($id, $quantity);
        // выполняем редирект обратно на ту страницу,
        // где была нажата кнопка «В корзину»
        return back();
    }

    /**
     * Увеличивает кол-во товара $id в корзине на единицу
     */
    public function plus($id)
    {
        $this->basket->increase($id);
        // выполняем редирект обратно на страницу корзины
        return redirect()->route('basket.index');
    }

    /**
     * Уменьшает кол-во товара $id в корзине на единицу
     */
    public function minus($id)
    {
        $this->basket->decrease($id);
        // выполняем редирект обратно на страницу корзины
        return redirect()->route('basket.index');
    }

    /**
     * Возвращает объект корзины; если не найден — создает новый
     */
//    private function getBasket() {
//        $basket_id = request()->cookie('basket_id');
//        if (!empty($basket_id)) {
//            try {
//                $this->basket = Basket::findOrFail($basket_id);
//            } catch (ModelNotFoundException $e) {
//                $this->basket = Basket::create();
//            }
//        } else {
//            $this->basket = Basket::create();
//        }
//        Cookie::queue('basket_id', $this->basket->id, 525600);
//    }

    /**
     * Удаляет товар с идентификаторм $id из корзины
     */
    public function remove($id)
    {
        $this->basket->remove($id);
        // выполняем редирект обратно на страницу корзины
        return redirect()->route('basket.index');
    }

    /**
     * Полностью очищает содержимое корзины покупателя
     */
    public function clear()
    {
        $this->basket->delete();
        // выполняем редирект обратно на страницу корзины
        return redirect()->route('basket.index');
    }

    /**
     * Сохранение заказа в БД
     */
    public function saveOrder(Request $request)
    {
        // проверяем данные формы оформления
//        dd($this, $request, $this->validate($request, [
//            'name' => 'required|max:255',
//            'email' => 'required|email|max:255',
//            'phone' => 'required|max:255',
//            'address' => 'required|max:255',
//        ]));
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:255',
            'address' => 'required|max:255',
        ]);

        // валидация пройдена, сохраняем заказ
        $basket = Basket::getBasket();
        $user_id = auth()->check() ? auth()->user()->id : null;
        $order = Order::create(
            $request->all() + ['amount' => $basket->getAmount(), 'user_id' => $user_id]
        );
//        self::$order = $order;
        $order_id = $order->id;
        $basket_id = $basket->id;

        foreach ($basket->products as $product) {
            $order->items()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'cost' => $product->price * $product->pivot->quantity,
            ]);
        }

        // уничтожаем корзину
        $basket->delete();
//        $basket_id = 0;

//        dd($this->basket, self::$order);

        return redirect()
            ->route('basket.success', compact('basket_id', 'order_id'))
            ->with('success', 'Ваш заказ успешно размещен');
    }

    /**
     * Сообщение об успешном оформлении заказа
     */
    public function success($basket_id, $order_id)
    {
//        dd(self::$order, $order_id);
//        $basket_id = $request->cookie('basket_id');
//        if (!empty($basket_id)) {
        // сюда покупатель попадает сразу после успешного оформления заказа
        //            $order = Order::findOrFail($basket_id);
        Cookie::forget('basket_id');
        $order = Order::findOrFail($order_id);
//            dd($basket_id, $order_id);
        return view('basket.success', compact('order'));
//        } else {
        // если покупатель попал сюда случайно, не после оформления заказа,
        // ему здесь делать нечего — отправляем на страницу корзины
        // точнее, не после начала формирования корзины, т.е. последняя ещё не создана
//            return redirect()->route('basket.index');
//        }
    }
}
