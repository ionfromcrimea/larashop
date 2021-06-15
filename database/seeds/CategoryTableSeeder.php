<?php
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryTableSeeder extends Seeder {
    public function run() {
        // создать 4 категории
        factory(App\Models\Category::class, 4)->create();
    }
}
