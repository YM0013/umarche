<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Shop;
use Illuminate\Support\Facades\Auth;
//リサイズしないパターン用
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:owners');

        $this->middleware(function ($request, $next) {
            $id = $request->route()->parameter('shop'); //shopのid取得 //文字列
            if (!is_null($id)) //数字にキャスト
            { // null判定
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopsOwnerId; // キャスト 文字列→数値に型変換
                $ownerId = Auth::id();
                if ($shopId !== $ownerId) { // 同じでなかったら
                    abort(404); // 404画面表示
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        //２行で書くパターン
        // $ownerId = Auth::id();
        // $shops = Shop::where('owner_id', $ownerId)->get();

        //一行で書くパターン
        $shops = Shop::where('owner_id', Auth::id())->get();

        return view(
            'owner.shops.index',
            compact('shops')
        );
    }

    public function edit($id)
    {
        //dd(Shop::findOrFail($id));
        $shop = Shop::findOrFail($id);
        return view('owner.shops.edit', compact('shop'));
    }

    public function update(Request $request, $id)
    {
        //リサイズしないパターン（putFileでファイル名生成）
        $imageFile = $request->image; //一時保存　パソコンでいったん保存している
        if (!is_null($imageFile) && $imageFile->isValid()) {
            Storage::putFile('public/shops', $imageFile);
        }
        //きちんとアップロードできているかの判定関数がisValid()
        //laravelマニュアル→より深く知る→ファイルストレージの中に開設されている
        //ファイルの保存を確認

        return redirect()->route('owner.shops.index');
    }
}
