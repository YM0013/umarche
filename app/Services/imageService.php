<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ImageService
{
  public static function upload($imageFile, $folderName)
  {
    if (is_array($imageFile)) {
      $file = $imageFile['image'];
    } else {
      $file = $imageFile;
    }

    $fileName = uniqid(rand() . '_');
    $extension = $file->extension();
    $fileNameToStore = $fileName . '.' . $extension;
    $resizedImage = InterventionImage::make($file)  //InterventionImageとは画像を扱うためのライブラリこれはmake()で画像を扱うことができる。InterventionImageを使うにはcomposer require intervention/imageをインストールする必要がある
      ->resize(1920, 1080, function ($constraint) { //InterventionImageをインストールしたら、composer.jsonに追加されるので、config/app.phpにInterventionImageServiceProviderを追加する必要がある
        $constraint->aspectRatio();                 //InterventionImageに赤波線がついているが、config/app.phpのaliasesにInterventionImageを設定しているので、無視しても問題なく動いている
      })->encode();

    Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage);
    return $fileNameToStore;
  }
}
