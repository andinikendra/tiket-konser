@php
    $steps = ['Pilih Kategori', 'Detail Pesanan', 'Metode Pembayaran', 'Pembayaran'];
@endphp
<nav class="steps" aria-label="Langkah pemesanan">
    <ol>
        @foreach ($steps as $i => $label)
            <li class="{{ $step === $i + 1 ? 'is-active' : '' }}" @if($step === $i + 1) aria-current="step" @endif>
                <span class="steps__num">{{ $i + 1 }}</span>
                <span>{{ $label }}</span>
            </li>
            @unless ($loop->last)
                <li class="steps__sep" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
                </li>
            @endunless
        @endforeach
    </ol>
</nav>