<x-tests.app>
  <x-slot name="header">ヘッダー２</x-slot>
コンポーネント２

<x-test-class-base classBaseMessage="メッセージです" />

<div class="mb-4"></div>
<x-test-class-base classBaseMessage="メッセージです" defaultMessage="初期値から変更しています" />
<x-tests.card title="タイトル1" content="本文" />
</x-tests.app>
