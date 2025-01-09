<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\SecondaryCategory;
use App\Models\image;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'information',
        'price',
        'is_selling',
        'sort_order',
        'secondary_category_id',
        'image1',
        'image2',
        'image3',
        'image4',

    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(SecondaryCategory::class, 'secondary_category_id');
    }

    /*
    image1としたいがDBのカラム名全く同じだとエラーが出てしまうので
    image1→imageFirstに変更した。変更はなんでもいいわかりやすければ
    また、belongsToは
    第1引数でモデルクラス
    第2引数でカラム名←これだけだとテーブル名の何を指定しているのかわからないので
    第3引数でどこと紐づいているかを指定できる
    */
    public function imageFirst()
    {
        return $this->belongsTo(Image::class, 'image1', 'id');
    }

    public function imageSecond()
    {
        return $this->belongsTo(Image::class, 'image2', 'id');
    }

    public function imageThird()
    {
        return $this->belongsTo(Image::class, 'image3', 'id');
    }

    public function imageFourth()
    {
        return $this->belongsTo(Image::class, 'image4', 'id');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function users()
    {
        return $this->belongsToMany(Product::class, 'carts')
            ->withPivot(['id', 'quantity']);
    }

    public function scopeAvailableItems($query)
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
        //groupBy('product_id')でproduct_idでグループ化して、sum()でquantityを合計している

        return $query
            ->joinSub($stocks, 'stock', function ($join) {          //上の$stocksで作った値を変数としてstockという名前に変換し
                $join->on('products.id', '=', 'stock.product_id');  //function($join)としてproduct.idテーブルとstock.product_idテーブルがくっつけています
            })                                                      //またshopsテーブルもくっつけないといけないので
            ->join('shops', 'products.shop_id', '=', 'shops.id')    //shopsという名前でproducts.shop_idテーブルとshops.idテーブルをくっつけています
            ->join(
                'secondary_categories',
                'products.secondary_category_id',
                '=',
                'secondary_categories.id'
            )
            ->join('images as image1', 'products.image1', '=', 'image1.id')
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
            );
        // dd($stocks, $products);
        // $products = Product::all();
    }

    public function scopeSortOrder($query, $sortOrder)
    {
        if ($sortOrder === null || $sortOrder === \Constant::SORT_ORDER['recommend']) {
            return $query->orderBy('sort_order', 'asc');
        }
        if ($sortOrder === \Constant::SORT_ORDER['higherPrice']) {
            return $query->orderBy('price', 'desc');
        }
        if ($sortOrder === \Constant::SORT_ORDER['lowerPrice']) {
            return $query->orderBy('price', 'asc');
        }
        if ($sortOrder === \Constant::SORT_ORDER['later']) {
            return $query->orderBy('products.created_at', 'desc');
        }
        if ($sortOrder === \Constant::SORT_ORDER['older']) {
            return $query->orderBy('products.created_at', 'asc');
        }
    }
}
