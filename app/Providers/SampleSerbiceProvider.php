<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SampleSerbiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //registerのほうでサービスコンテナに登録する処理を書く
        app()->bind('serviceProviderTest', function () {
            return 'サービスプロバイダーテスト';
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
