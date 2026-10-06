@extends('layouts.app')

@section('title', 'Pembayaran - ' . $event->title)

@section('stepper')
    @include('partials.stepper', ['step' => 4])
@endsection

@section('content')
<div class="container checkout">
    <div class="checkout__main">
        <div class="panel form">
            <h2 class="panel__title">Menunggu pembayaran</h2>
            <p>Kode pesanan <strong>{{ $cart['order_code'] }}</strong></p>
            <p>Metode: {{ $methods[$cart['method']] }}</p>
            <p>Total bayar: <strong class="total">{{ \App\Support\Rupiah::format($total) }}</strong></p>
            <p class="muted small">
                Halaman ini baru kerangka. Sambungkan payment gateway (mis. Midtrans atau Xendit)
                di CheckoutController::payment() untuk membuat transaksi sungguhan.
            </p>
            <a href="{{ route('home') }}" class="btn btn--primary">Kembali ke beranda</a>
        </div>
    </div>

    @include('checkout._summary')
</div>
@endsection
