<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;


class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
    }
    public function index()
    {
        $stocks = DB::table('t_stocks')
            ->select(
                'product_id',
                DB::raw('sum(quantity) as quantity') //raw()で()の中でSQL文を直接書くことが出来る
            )
            ->groupBy('product_id')
            ->having('quantity', '>=', 1);
        //Having・・groupByの後に条件指定
        //Where・・groupByより前に条件指定
        $products = DB::table('products')
            ->joinSub($stocks, 'stock', function ($join) {          //上の$stocksで作った値を変数としてstockという名前に変換し
                $join->on('products.id', '=', 'stock.product_id');   //function($join)としてproduct.idテーブルとstock.product_idテーブルがくっつけています
            })                                                      //またshopsテーブルもくっつけないといけないので
            ->join('shops', 'products.shop_id', '=', 'shops.id')    //shopsという名前でproducts.shop_idテーブルとshops.idテーブルをくっつけています
            ->join(
                'secondary_categories',
                'products.secondary_category_id',
                '=',
                'secondary_categories.id'
            )
            ->join('images as image1', 'products.image1', '=', 'image1.id')
            ->join('images as image2', 'products.image2', '=', 'image2.id')
            ->join('images as image3', 'products.image3', '=', 'image3.id')
            ->join('images as image4', 'products.image4', '=', 'image4.id')
            ->where('shops.is_selling', true)                                   //これで確認したいテーブルが全てくっついたので、shops.is_sellingがtrueかつ
            ->where('products.is_selling', true)                                //products.is_sellingがtrueのテーブルのみ取得することが出来る
            ->select(
                'products.id as id',                                            //〇〇as△△を使うことで〇〇はテーブル名の△△を指定するという使い方ができる
                'products.name as name',
                'products.price',
                'products.sort_order as sort_order',
                'products.information',
                'secondary_categories.name as category',
                'image1.filename as filename'
            )
            ->get();

        // dd($stocks, $products);
        // $products = Product::all();
        return view('user.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $quantity = Stock::where('product_id', $product->id)
            ->sum('quantity'); //一つの商品の在庫を取ることになるのでStockになっている
        if ($quantity > 9) {
            $quantity = 9;
        }

        return view('user.show', compact('product', 'quantity'));
    }
}
