<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Shop;
use Illuminate\Support\Facades\Auth;
//リサイズしないパターン用
use Illuminate\Support\Facades\Storage;
use InterventionImage;
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;

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

    public function update(UploadImageRequest $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'information' => ['required', 'string',  'max:1000',],
            'is_selling' => ['required',],
        ]);

        //リサイズしないパターン（putFileでファイル名生成）
        $imageFile = $request->image; //一時保存　パソコンでいったん保存している
        if (!is_null($imageFile) && $imageFile->isValid()) {
            $fileNameToStore = ImageService::upload($imageFile, 'shops');
        }

        $shop = Shop::findOrFail($id);
        $shop->name = $request->name;
        $shop->information = $request->information;
        $shop->is_selling = $request->is_selling;
        if (!is_null($imageFile) && $imageFile->isValid()) {
            $shop->filename = $fileNameToStore;
        }
        $shop->save();

        return redirect()
            ->route('owner.shops.index')
            ->with([
                'message' => '店舗情報を更新しました',
                'status' => 'info'
            ]);;
    }

    //Storage::putを使うとストレージ指定、第１引数でファイル名、第２引数で中身となる
    //きちんとアップロードできているかの判定関数がisValid()
    //laravelマニュアル→より深く知る→ファイルストレージの中に開設されている
    //ファイルの保存を確認

    //Storage::putFile('public/shops', $imageFile);　リサイズなしの場合、リサイズする場合は方がオブジェクトから画像になるため使えなくなる
    // $fileName = uniqid(rand() . '_');
    // $extension = $imageFile->extension();
    // $fileNameToStore = $fileName . '.' . $extension;
    // $resizedImage = InterventionImage::make($imageFile)
    //     ->resize(1920, 1080, function ($constraint) {
    //         $constraint->aspectRatio();
    //     })->encode();
    //
    //Storage::put('public/shops/' . $fileNameToStore, $resizedImage);
}
