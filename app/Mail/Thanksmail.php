<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SplSubject;

class Thanksmail extends Mailable
{
    use Queueable, SerializesModels;

    //jobsフォルダのsendThanksMail.phpで設定した
    //send(new ThanksMail($this->products, $this->user))の
    //productsとuserを使えるように変数の箱を作る必要がある

    public $products;
    public $user;

    public function __construct($products, $user)
    {
        $this->products = $products;
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.thanks')
            ->subject('ご購入ありがとうございます');
    }
}
