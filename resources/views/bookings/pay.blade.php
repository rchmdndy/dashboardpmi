@extends('layouts.app')
@section('head')
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{config('midtrans.client_key')}}"></script>
@endsection
@section('content')

    <div style="display:flex; align-content: center; justify-content: center">
        <div id="snap-container"></div>
    </div>

    <script type="text/javascript">
            // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token.
            // Also, use the embedId that you defined in the div above, here.
            window.snap.embed('{{$snap_token}}', {
                embedId: 'snap-container',
                onSuccess: function (result) {
                    /* You may add your own implementation here */
                    alert("payment success!"); console.log(result);
                },
                onPending: function (result) {
                    /* You may add your own implementation here */
                    alert("wating your payment!"); console.log(result);
                },
                onError: function (result) {
                    /* You may add your own implementation here */
                    alert("payment failed!"); console.log(result);
                },
                onClose: function () {
                    /* You may add your own implementation here */
                    alert('you closed the popup without finishing the payment');
                }
            });
    </script>
@endsection
