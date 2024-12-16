<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Models\Stock;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            OwnerSeeder::class,
            ShopSeeder::class,
            ImageSeeder::class,
            CategorySeeder::class,
            //ProductSeeder::class,
            //StockSeeder::class,
            UserSeeder::class,
        ]);
        //外部キー制約の都合でShopやImageが作られないとプロダクトを一緒に作ってしまうとエラーが出てしまうので
        //callの後に記載する必要あり
        Product::factory(100)->create();
        Stock::factory(100)->create();
    }
}
