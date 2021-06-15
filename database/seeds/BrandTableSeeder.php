<?php
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandTableSeeder extends Seeder {

    public function run() {
        // создать 4 бренда
        factory(App\Models\Brand::class, 4)->create();
    }
}
