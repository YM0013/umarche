<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Arr;
use App\Models\Stock;

class CartController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        $totalPrice = 0;

        foreach ($products as $product) {
            $totalPrice += $product->price * $product->pivot->quantity;
        }
        //$productsはユーザーが持っている商品の情報が入っている、
        //$product->pivot->quantityで中間テーブルのquantityを取得している
        //$totalPriceは商品の値段と数量を掛け合わせている
        //dd($products, $totalPrice);

        return view(
            'user.cart',
            compact('products', 'totalPrice')
        ); //compact()で複数の変数をviewに渡すことができる
    }

    public function add(Request $request)
    {
        $itemInCart = Cart::where('product_id', $request->product_id)
            ->where('user_id', Auth::id()) //別のユーザーがヒットする可能性があるので、ログインしているユーザーのIDを取得してプロダクトIDとユーザーIDが一致するものを取得
            ->first(); //1件のアイテムだけヒットすればいいので、1件だけ取得する

        if ($itemInCart) {
            $itemInCart->quantity += $request->quantity; //もともとカートには1つ入っていていて、追加した場合はさらにquantityを追加する
            $itemInCart->save(); //これはsave()を書かないと保存されない
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,

            ]);
        }

        return redirect()->route('user.cart.index');
    }

    public function delete($id)
    {
        Cart::where('product_id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('user.cart.index');
    }

    public function checkout()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;

        foreach ($products as $product) {
            $lineItems = []; //StripeのAPIに渡すための配列を作成,ちなみにStripeで商品はlineItemsという名前で使われている
            foreach ($products as $product) {
                $quantity = '';
                $quantity = Stock::where('product_id', $product->id)->sum('quantity');

                if ($product->pivot->quantity > $quantity) { //$product->pivot->quantityでカートの中の商品の数量を、$quantityで在庫の数量を取得している
                    return redirect()->view('user.cart.index'); //商品はカートの中に複数あったとしても、１つでも在庫が足りない場合はカートに戻すようにする。
                } else {
                    $price_data = ([
                        'unit_amount' => $product->price,
                        'currency' => 'jpy',
                        'product_data' => $price_data = ([
                            'name' => $product->name,
                            'description' => $product->information,
                        ]),
                    ]);

                    $lineItem = [
                        'price_data' => $price_data,
                        'quantity' => $product->pivot->quantity,
                    ];

                    array_push($lineItems, $lineItem);
                    //array_push()で配列の最後に要素を追加する
                    //ここでは$lineItemsに$lineItemを追加している
                    //array_push()は第1引数に追加したい配列、第2引数に追加したい要素を指定する
                }
            }
            //dd($lineItems);

            //在庫チェックが終わったので、Stripeに渡す前に在庫を減らす処理を書く
            foreach ($products as $product) {
                Stock::create([
                    'product_id' => $product->id,
                    'type' => \Constant::PRODUCT_LIST['reduce'], //ownerのProductControllerで定義したPRODUCT_LISTのreduceを指定
                    'quantity' => $product->pivot->quantity * -1, //カートの中の在庫数を取りたいので、$product->pivot->quantityを指定　在庫を現状させるので-1をかける
                ]);
            }

            //dd('test');

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [$lineItems],               //上記で作成したlineItemsの配列がforeachで回っているので、その配列をそのまま渡している
                'mode' => 'payment',                        //支払いのモードを指定(１回支払いの場合)（サブスクリプションはまた別）
                'success_url' => route('user.items.index'), //支払いが成功した場合アイテム一覧にリダイレクトがかかる
                'cancel_url' => route('user.cart.index'),   //支払いがキャンセルされた場合カートにリダイレクトがかかる
            ]);
            //viewにreturnで渡す前に公開鍵も合わせて渡す必要がある
            //つまり公開鍵と秘密鍵が一緒になっているので決済が出来るという事になる
            $publicKey = env('STRIPE_PUBLIC_KEY');

            return view(
                'user.checkout',
                compact('session', 'publicKey')
            );

            //sessionには商品情報なども全て入っていることになる
        }
    }
}
