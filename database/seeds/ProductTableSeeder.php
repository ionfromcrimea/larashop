<?php
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductTableSeeder extends Seeder {
    public function run() {
        // создать 12 товаров
        factory(App\Models\Product::class, 12)->create();
    }
}
