<?php

namespace App\Constants;

class Common
{

  //定数を作る場合はconstというキーワードが必要
  const PRODUCT_ADD = '1';
  const PRODUCT_REDUCE = '2';

  //連想配列で管理することが出来る
  //同じクラス内でconstを選択する場合はself::が必要になる。
  const PRODUCT_LIST = [
    'add' => self::PRODUCT_ADD,
    'reduce' => self::PRODUCT_REDUCE,
  ];
}
