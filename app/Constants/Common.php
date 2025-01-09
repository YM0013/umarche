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

  const ORDER_RECOMMEND = '0';
  const ORDER_HIGHER = '1';
  const ORDER_LOWER = '2';
  const ORDER_LATER = '3';
  const ORDER_OLDER = '4';

  const SORT_ORDER = [
    'recommend' => self::ORDER_RECOMMEND,
    'higherPrice' => self::ORDER_HIGHER,
    'lowerPrice' => self::ORDER_LOWER,
    'later' => self::ORDER_LATER,
    'older' => self::ORDER_OLDER,
  ];
}
