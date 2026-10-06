@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . $event->title)

@section('stepper')
    @include('partials.stepper', ['step' => 2])
@endsection

@section('content')
<div class="container checkout">
    <div class="checkout__main">
        <form method="POST" action="{{ route('checkout.detail.save', $event) }}" class="panel form">
            @csrf
            <h2 class="panel__title">Data pemesan</h2>
            <p class="muted small">E-tiket akan dikirim ke email yang kamu isi di sini.</p>

            @foreach ([
                'name'  => ['Nama lengkap', 'text', 'name'],
                'email' => ['Email', 'email', 'email'],
                'phone' => ['Nomor WhatsApp', 'tel', 'tel'],
            ] as $field => [$label, $type, $auto])
                <label class="field">
                    <span>{{ $label }}</span>
                    <input type="{{ $type }}" name="{{ $field }}" autocomplete="{{ $auto }}"
                           value="{{ old($field, $cart['buyer'][$field] ?? '') }}" required>
                    @error($field)<small class="error">{{ $message }}</small>@enderror
                </label>
            @endforeach

            <button type="submit" class="btn btn--primary">Lanjut ke metode pembayaran</button>
        </form>
    </div>

    @include('checkout._summary')
</div>
@endsection
