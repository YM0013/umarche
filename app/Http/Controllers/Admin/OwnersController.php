<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner; //Eloquent エロクアント
use Illuminate\Support\Facades\DB; //QueryBuilder クエリビルダー
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Throwable;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;

class OwnersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        // $date_now = Carbon::now();
        // $data_parse = Carbon::parse(now());
        // echo $date_now->year;
        // echo $data_parse;

        // $e_all = Owner::all();  //テストとしてEloquentの変数を設定＝$e
        // $q_get = DB::table('owners')->select('name', 'created_at')->get();    //上記と同様QueryBuilderの変数を設定=$q
        //$q_first = DB::table('owners')->select('name')->first();
        // $c_test = collect([
        //     'name' => 'テスト'
        // ]);

        // var_dump($q_first);
        // dd($e_all, $q_get, $q_first, $c_test);

        $owners = Owner::select('id', 'name', 'email', 'created_at')
            ->paginate(3);

        return view(
            'admin.owners.index',
            compact('owners')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$request->name;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:owners'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            DB::transaction(function () use ($request) {
                $owner = Owner::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                Shop::create([
                    'owner_id' => $owner->id,
                    'name' => '店名を入力してください',
                    'information' => '',
                    'filename' => '',
                    'is_selling' => true,
                ]);
            }, 2);
        } catch (Throwable $e) {
            Log::error($e);
            throw ($e);
        }

        //上の記述例外＋ログlaravel10の機能
        // Logファサードを利用している
        // Log::emergency($message);　  緊急事態は発生したとき              システム全体が利用不能など  例：データベースが完全にダウン、外部サービスが完全停止。
        // Log::alert($message);        すぐに対応が必要な状態              システムは稼働しているが、すぐに解決しなければ大問題になる可能性    例: ディスク容量の急激な低下、APIキーの期限切れ間近。
        // Log::critical($message);     致命的な問題に対応するログ          システムの重要な部分が動作しないが、システム全体は完全に停止していない  例: 主要なサービスが利用不能、一部のユーザーに重大な影響。
        // Log::error($message);        実行時のエラーに関するログ          例外やエラーが発生し、アプリケーションの一部が正常に動作しない場合。   例: 無効なユーザー入力によるエラー、ファイルが見つからない。 
        // Log::warning($message);      潜在的な問題が発生したときのログ    問題が発生する可能性があるが、システムはまだ動作している。  例: 古いバージョンのAPIが使用されている、非推奨機能が呼び出された。
        // Log::notice($message);       正常な動作中の重要なイベントを記録    問題ではないが、注意を払うべき重要な状況。  例: ユーザーが新しい設定を適用、管理者がログイン。
        // Log::info($message);         一般的な情報を記録。                システムの状態やイベントに関する情報を記録。    例: ユーザーがログイン、タスクが完了。
        // Log::debug($message);        デバッグ情報を記録。                システムの動作を詳細に記録し、開発時のデバッグに役立てる。  例: 変数値の追跡、処理フローの確認。
        return redirect()
            ->route('admin.owners.index')
            ->with([
                'message' => 'オーナー登録を実施しました。',
                'status' => 'info'
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $owner = Owner::findOrFail($id);
        // dd($owner);
        return view('admin.owners.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->password = Hash::make($request->getPassword);
        $owner->save();

        return redirect()
            ->route('admin.owners.index')
            ->with([
                'message' => 'オーナー情報を更新しました',
                'status' => 'info'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //dd('削除処理');
        Owner::findOrFail($id)->delete(); //ソフトデリート

        return redirect()
            ->route('admin.owners.index')
            ->with([
                'message' => 'オーナー情報を削除しました',
                'status' => 'alert'
            ]);
    }

    public function expiredOwnerIndex()
    {
        $expiredOwners = Owner::onlyTrashed()->get();
        return view(
            'admin.expired-owners',
            compact('expiredOwners')
        );
    }
    public function expiredOwnerDestroy($id)
    {
        Owner::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.expired-owners.index');
    }
    public function expiredOwnerRestore($id)
    {
        Owner::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.expired-owners.index')
            ->with([
                'message' => 'オーナー情報を復帰しました',
                'status' => 'info',
            ]);
    }
}
