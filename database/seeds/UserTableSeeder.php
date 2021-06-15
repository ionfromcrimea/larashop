<?php
use Illuminate\Database\Seeder;
use App\Models\Brand;

class UserTableSeeder extends Seeder {
    public function run() {
        // создать 5 юзеров
        factory(App\Models\User::class, 5)->create();
    }
}
