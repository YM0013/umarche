<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        // dd($products, $totalPrice);

        return view(
            'user.cart',
            compact('products', 'totalPrice')
        );
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
}
