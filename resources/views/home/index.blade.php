@extends('layouts.app')

@section('title', 'Tiket Konser - Beli Tiket Event')
@section('width', 'container--wide')

@section('content')
<section class="container container--wide hero">
    <h1>Cari tiket konser favoritmu</h1>
    <p class="muted">Pilih event, pilih tiket, selesai.</p>

    <div class="grid-events" style="margin-top: 28px;">
        @foreach ($events as $event)
            @include('partials.event-card', ['event' => $event])
        @endforeach
    </div>
</section>
@endsection