<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\SecondaryCategory;
use App\Models\image;
use App\Models\Stock;

class Product extends Model
{
    use HasFactory;

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

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }
}
