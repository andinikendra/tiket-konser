<aside class="checkout__side">
    <div class="panel">
        <h2 class="panel__title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 8V7a5 5 0 0 1 10 0v1h2.2l.8 12.2A1.5 1.5 0 0 1 18.5 22h-13A1.5 1.5 0 0 1 4 20.2L4.8 8H7Zm2 0h6V7a3 3 0 0 0-6 0v1Z"/></svg>
            Rincian Pesanan
        </h2>
        <div class="box">
            <p class="summary-event">{{ $event->title }}</p>
            @foreach ($items as $item)
                <div class="line">
                    <div>
                        <div>{{ $item['name'] }}</div>
                        <div class="muted small">x{{ $item['qty'] }}</div>
                    </div>
                    <span>{{ \App\Support\Rupiah::format($item['price'] * $item['qty']) }}</span>
                </div>
            @endforeach
            <div class="row row--line"><span class="muted">Subtotal</span><span>{{ \App\Support\Rupiah::format($total) }}</span></div>
            <div class="row row--line"><span>Total {{ $totalQty }} Item</span><strong class="total">{{ \App\Support\Rupiah::format($total) }}</strong></div>
        </div>
    </div>
</aside>
