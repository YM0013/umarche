<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('primary_categories')->insert([
            [
                'name' => '主菜',
                'sort_order' => 1,
            ],

            [
                'name' => '副菜',
                'sort_order' => 2,
            ],

            [
                'name' => 'その他',
                'sort_order' => 3,
            ],
        ]);

        DB::table('secondary_categories')->insert([
            [
                'name' => '焼き魚',
                'sort_order' => 1,
                'primary_category_id' => 1,
            ],

            [
                'name' => '煮魚',
                'sort_order' => 2,
                'primary_category_id' => 1,
            ],

            [
                'name' => '煮物系',
                'sort_order' => 3,
                'primary_category_id' => 2,
            ],

            [
                'name' => 'サラダ系',
                'sort_order' => 4,
                'primary_category_id' => 2,
            ],

            [
                'name' => '漬物',
                'sort_order' => 5,
                'primary_category_id' => 3,
            ],
        ]);
    }
}
