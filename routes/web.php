<?php

use Illuminate\Support\Facades\Route;

/*
 * Главная страница интернет-магазина
 */
Route::get('/', 'IndexController')->name('index');

/*
 * Страницы «Доставка», «Контакты» и прочие
 */
Route::get('/page/{page:slug}', 'PageController')->name('page.show');

/*
 * Каталог товаров: категория, бренд и товар
 */
Route::group([
    'as' => 'catalog.', // имя маршрута, например catalog.index
    'prefix' => 'catalog', // префикс маршрута, например catalog/index
], function () {
    // главная страница каталога
    Route::get('index', 'CatalogController@index')
        ->name('index');
    // категория каталога товаров
    Route::get('category/{category:slug}', 'CatalogController@category')
        ->name('category');
    // бренд каталога товаров
    Route::get('brand/{brand:slug}', 'CatalogController@brand')
        ->name('brand');
    // страница товара каталога
    Route::get('product/{product:slug}', 'CatalogController@product')
        ->name('product');
});

/*
 * Корзина покупателя
 */
Route::group([
    'as' => 'basket.', // имя маршрута, например basket.index
    'prefix' => 'basket', // префикс маршрута, например bsaket/index
], function () {
    // список всех товаров в корзине
    Route::get('index', 'BasketController@index')
        ->name('index');
    // страница с формой оформления заказа
    Route::get('checkout', 'BasketController@checkout')
        ->name('checkout');
    // получение данных профиля для оформления
    Route::post('profile', 'BasketController@profile')
        ->name('profile');
    // отправка данных формы для сохранения заказа в БД
    Route::post('saveorder', 'BasketController@saveOrder')
        ->name('saveorder');
    // страница после успешного сохранения заказа в БД
    Route::get('success', 'BasketController@success')
        ->name('success');
    // отправка формы добавления товара в корзину
    Route::post('add/{id}', 'BasketController@add')
        ->where('id', '[0-9]+')
        ->name('add');
    // отправка формы изменения кол-ва отдельного товара в корзине
    Route::post('plus/{id}', 'BasketController@plus')
        ->where('id', '[0-9]+')
        ->name('plus');
    // отправка формы изменения кол-ва отдельного товара в корзине
    Route::post('minus/{id}', 'BasketController@minus')
        ->where('id', '[0-9]+')
        ->name('minus');
    // отправка формы удаления отдельного товара из корзины
    Route::post('remove/{id}', 'BasketController@remove')
        ->where('id', '[0-9]+')
        ->name('remove');
    // отправка формы для удаления всех товаров из корзины
    Route::post('clear', 'BasketController@clear')
        ->name('clear');
});

/*
 * Регистрация, вход в ЛК, восстановление пароля
 */
Route::name('user.')->prefix('user')->group(function () {
    Auth::routes();
});

/*
 * Личный кабинет зарегистрированного пользователя
 */
Route::group([
    'as' => 'user.', // имя маршрута, например user.index
    'prefix' => 'user', // префикс маршрута, например user/index
    'middleware' => ['auth'] // один или несколько посредников
], function () {
    // главная страница личного кабинета пользователя
    Route::get('index', 'UserController@index')->name('index');
    // CRUD-операции над профилями пользователя
    Route::resource('profile', 'ProfileController');
    // просмотр списка заказов в личном кабинете
    Route::get('order', 'OrderController@index')->name('order.index');
    // просмотр отдельного заказа в личном кабинете
    Route::get('order/{order}', 'OrderController@show')->name('order.show');
});

/*
 * Панель управления магазином для администратора сайта
 */
Route::group([
    'as' => 'admin.', // имя маршрута, например admin.index
    'prefix' => 'admin', // префикс маршрута, например admin/index
    'namespace' => 'Admin', // пространство имен контроллера
    'middleware' => ['auth', 'admin'] // один или несколько посредников
], function () {
    // главная страница панели управления
    Route::get('index', 'IndexController')->name('index');
    // CRUD-операции над категориями каталога
    Route::resource('category', 'CategoryController');
    // CRUD-операции над брендами каталога
    Route::resource('brand', 'BrandController');
    // CRUD-операции над товарами каталога
    Route::resource('product', 'ProductController');
    // доп.маршрут для показа товаров категории
    Route::get('product/category/{category}', 'ProductController@category')
        ->name('product.category');
    // просмотр и редактирование заказов
    Route::resource('order', 'OrderController', ['except' => [
        'create', 'store', 'destroy'
    ]]);
    // просмотр и редактирование пользователей
    Route::resource('user', 'UserController', ['except' => [
        'create', 'store', 'show', 'destroy'
    ]]);
    // CRUD-операции над страницами сайта
    Route::resource('page', 'PageController');
    // загрузка изображения из wysiwyg-редактора
    Route::post('page/upload/image', 'PageController@uploadImage')
        ->name('page.upload.image');
    // удаление изображения в wysiwyg-редакторе
    Route::delete('page/remove/image', 'PageController@removeImage')
        ->name('page.remove.image');
});

Route::get('/feedback', 'FeedbackController@index')->name('feedback.index');
Route::post('/feedback', 'FeedbackController@send')->name('feedback.send');



/*
|--------------------------------------------------------------------------
| Web Routes - web.php до наведения порядка
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/error', function () {
    return view('errors.404');
});

// маршрут для главной страницы без указания метода
Route::get('/', 'IndexController')->name('index');

Route::get('/catalog/index', 'CatalogController@index')->name('catalog.index');
Route::get('/catalog/category/{category:slug}', 'CatalogController@category')->name('catalog.category');
Route::get('/catalog/brand/{brand:slug}', 'CatalogController@brand')->name('catalog.brand');
Route::get('/catalog/product/{product:slug}', 'CatalogController@product')->name('catalog.product');

Route::get('/basket/index', 'BasketController@index')->name('basket.index');
Route::get('/basket/checkout', 'BasketController@checkout')->name('basket.checkout');
Route::post('/basket/add/{id}', 'BasketController@add')
    ->where('id', '[0-9]+')
    ->name('basket.add');
Route::post('/basket/plus/{id}', 'BasketController@plus')
    ->where('id', '[0-9]+')
    ->name('basket.plus');
Route::post('/basket/minus/{id}', 'BasketController@minus')
    ->where('id', '[0-9]+')
    ->name('basket.minus');
Route::post('/basket/remove/{id}', 'BasketController@remove')
    ->where('id', '[0-9]+')
    ->name('basket.remove');
Route::post('/basket/clear', 'BasketController@clear')->name('basket.clear');

//Auth::routes();
Route::name('user.')->prefix('user')->group(function () {
//    Route::get('index', 'UserController@index')->name('index');
    Auth::routes();
});
Route::group([
    'as' => 'user.', // имя маршрута, например user.index
    'prefix' => 'user', // префикс маршрута, например user/index
    'middleware' => ['auth'] // один или несколько посредников
], function () {
    // главная страница личного кабинета пользователя
    Route::get('index', 'UserController@index')->name('index');
    // CRUD-операции над профилями пользователя
    Route::resource('profile', 'ProfileController');
    // просмотр списка заказов в личном кабинете
    Route::get('order', 'OrderController@index')->name('order.index');
    // просмотр отдельного заказа в личном кабинете
    Route::get('order/{order}', 'OrderController@show')->name('order.show');
});

Route::get('/home', 'HomeController@index')->name('home');

// первый способ добавления посредников (квадратные скобки для параметров middleware() - ???
//Route::namespace('Admin')->name('admin.')->prefix('admin')->middleware('auth', 'admin')->group(function () {
//    Route::get('index', 'IndexController')->name('index');
//});

// второй способ добавления посредников
Route::group([
    'as' => 'admin.', // имя маршрута, например admin.index
    'prefix' => 'admin', // префикс маршрута, например admin/index
    'namespace' => 'Admin', // пространство имен контроллера
    'middleware' => ['auth', 'admin'] // один или несколько посредников
], function () {
    Route::get('index', 'IndexController')->name('index');
});

// это первый вариант указания пространства имен
//Route::name('admin.')->prefix('admin')->group(function () {
//    Route::get('index', 'Admin\IndexController')->name('index');
//});

// это второй вариант указания пространства имен
//Route::namespace('Admin')->name('admin.')->prefix('admin')->group(function () {
//    Route::get('index', 'IndexController')->name('index');
//});

Route::post('/basket/saveorder', 'BasketController@saveOrder')->name('basket.saveorder');
Route::get('/basket/success/{basket_id}/{order_id}', 'BasketController@success')->name('basket.success');

Route::group([
    'as' => 'admin.', // имя маршрута, например admin.index
    'prefix' => 'admin', // префикс маршрута, например admin/index
    'namespace' => 'Admin', // пространство имен контроллера
    'middleware' => ['auth', 'admin'] // один или несколько посредников
], function () {
    // главная страница панели управления
    Route::get('index', 'IndexController')->name('index');
    // CRUD-операции над категориями каталога
    Route::resource('category', 'CategoryController');
    // CRUD-операции над брендами каталога
    Route::resource('brand', 'BrandController');
    // CRUD-операции над товарами каталога
    Route::resource('product', 'ProductController');
    Route::get('product/category/{category}', 'ProductController@category')
        ->name('product.category');
    // просмотр и редактирование заказов
    Route::resource('order', 'OrderController', ['except' => [
        'create', 'store', 'destroy'
    ]]);
    // просмотр и редактирование пользователей
    Route::resource('user', 'UserController', ['except' => [
        'create', 'store', 'show', 'destroy'
    ]]);
    // CRUD-операции над страницами сайта
    Route::resource('page', 'PageController');
    Route::post('page/upload/image', 'PageController@uploadImage')
        ->name('page.upload.image');
    // удаление изображения в редакторе
    Route::delete('page/remove/image', 'PageController@removeImage')
        ->name('page.remove.image');
});

//Route::get('page/{page}', 'PageController')->name('page.show');
Route::get('/page/{page:slug}', 'PageController')->name('page.show');

//Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');
