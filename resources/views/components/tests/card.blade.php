@props([
    'title' => '初期値タイトルです。',
    'message' => '初期値です。',
    'content' => '初期値本文です。'
])

<div {{ $attributes ->merge([
    'class' => 'border-2 shasow-md w-1/4 p-2'
   ]) }} >
  <div>{{ $title }}</div>
  <div>画像</div>
  <div>{{ $content }}</div>
  <div>{{ $message }}</div>
  
</div>