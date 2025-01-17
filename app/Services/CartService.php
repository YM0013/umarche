<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Cart;
use App\Jobs\SendThanksMail;

class CartService
{
  public static function getItemsInCart($items)
  {
    $products = []; //空の配列を準備

    //dd($items);
    foreach ($items as $item) //カート内の商品を1つずつ処理
    {
      $p = Product::findOrFail($item->product_id);
      $owner = $p->shop->owner->select('name', 'email')->first()->toArray(); //オーナー情報
      $values = array_values($owner); //連想配列の値を取得
      $keys = ['ownerName', 'email'];
      $ownerInfo = array_combine($keys, $values);
      //dd($ownerInfo);
      $product = Product::where('id', $item->product_id)
        ->select('id', 'name', 'price')->get()->toArray();

      $quantity = Cart::where('product_id', $item->product_id)
        ->select('quantity')->get()->toArray();

      $result = array_merge($product[0], $ownerInfo, $quantity[0]);
      array_push($products, $result);
    }
    //dd($products);
    return $products; //新しい配列を返す
  }
}
