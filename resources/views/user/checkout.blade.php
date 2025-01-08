<p>決済ページへリダイレクトします</p>
<script src="https://js.stripe.com/v3/"></script>  {{--  --}}
<script>
  const publicKey = '{{$publicKey}}' 
  const stripe = Stripe(publicKey)
  window.onload = function()
  {
    stripe.redirectToCheckout({ //redirectToCheckoutでチェックアウトページに飛ばしている 
      sessionId: '{{ $session->id}}' //sessionIDでセッションIDを渡している　$session->idはcartControllerで作成した$session = \Stripe\Checkout\Session::create();の$sessionちなみにコントローラで$sessionを作った時点でIDが降られているのでidを指定できる
    }).then(function(result){
      window.location.href = '{{ route('user.cart.cancel')}}'//もしエラーが出た場合はカートページに飛ばしている
    });
  }
</script>