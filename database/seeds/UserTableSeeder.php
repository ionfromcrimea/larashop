<?php
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder {
    public function run() {
        // создать 5 юзеров
        factory(App\User::class, 5)->create();
    }
}
