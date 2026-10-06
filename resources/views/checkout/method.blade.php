@extends('layouts.app')

@section('title', 'Metode Pembayaran - ' . $event->title)

@section('stepper')
    @include('partials.stepper', ['step' => 3])
@endsection

@section('content')
<div class="container checkout">
    <div class="checkout__main">
        <form method="POST" action="{{ route('checkout.method.save', $event) }}" class="panel form">
            @csrf
            <h2 class="panel__title">Pilih metode pembayaran</h2>

            @foreach ($methods as $key => $label)
                <label class="option">
                    <input type="radio" name="method" value="{{ $key }}" @checked(old('method', $cart['method'] ?? null) === $key) required>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
            @error('method')<small class="error">{{ $message }}</small>@enderror

            <button type="submit" class="btn btn--primary">Lanjut ke pembayaran</button>
        </form>
    </div>

    @include('checkout._summary')
</div>
@endsection
