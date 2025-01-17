<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Mail\ThanksMail;


class SendThanksMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //カートコントローラーのcheckoutのdispatch($products, $user)で設定した
    //productsとuserを使えるように変数の箱を作る必要がある
    public $products;
    public $user;

    public function __construct($products, $user)
    {
        $this->products = $products;
        $this->user = $user;
    }
    //ここに実行する処理を書く
    public function handle()
    {
        //Mail::to('test@example.com')->send(new TestMail());
        //$this->userでユーザー情報の中のEメールの列を探してくれる
        Mail::to($this->user)
            ->send(new ThanksMail($this->products, $this->user));
    }
}
