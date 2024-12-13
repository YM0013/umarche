<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Dashboard') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 bg-white border-b border-gray-200">
                <x-flash-message status="session('status')" />    
                  <div class="flex justify-end mb-4">
                    <button onclick="location.href='{{route('owner.products.create')}}'"  class="text-white bg-purple-500 border-0 py-2 px-8 focus:outline-none hover:bg-purple-600 rounded text-lg">新規登録する</button>
                  </div>   
                  <div class="flex flex-wrap">
                        @foreach ( $ownerInfo as $owner)
                            @foreach ($owner->shop->product as $product)
                                <div class = "w-1/4 p-2 md:p-4">
                                    <a href = "{{route('owner.products.edit',['product'=> $product->id])}}">
                                        <div class="border rounded-md p-2 md:p-4">
                                            <x-thumbnail :filename="$product->imageFirst->filename" type="products" />
                                            {{--$product->imageFirst->filenameはこのままでは、画像に対して1つずつSQL文が発行されるので
                                            画像が増えれば増えるほど、処理が重くなってしまう。通称N＋１問題
                                            対策するにはlaravelだとEagerLoading処理が必要公式ドキュメントは
                                            「laravelドキュメント->Eloquent ORM->リレーション->Eagerロード」に書いてある
                                            書き方は$books = Book::with('author')->get();とする
                                            ちなみにこれにおいてのリレーションは「->」ではなく「.」で繋げる　--}}    
                                            {{--<div class="text-gray-700">{{$product->name}}</div>
                                            適応するとselect * from `images` where `images`.`id` in (1, 2, 3, 4)
                                            このようにまとまる--}}
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
