@extends('layouts.app')

@section('title', $event->title . ' - Tiket Konser')

@section('stepper')
    @include('partials.stepper', ['step' => 1])
@endsection

@section('content')
<div class="container checkout">
    <div class="checkout__main">
        <div class="hero-poster" style="background: {{ $event->poster_gradient }}">
            @if ($event->poster_url)
                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}">
            @else
                <span class="poster__title">{{ $event->title }}</span>
            @endif
        </div>
        <h1 class="sr-only">{{ $event->title }}</h1>

        <span class="chip">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5V10a2 2 0 0 0 0 4v2.5a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 16.5V14a2 2 0 0 0 0-4Zm8 .5v2h2V8Zm0 3v2h2v-2Zm0 3v2h2v-2Z"/></svg>
            Kategori Tiket
        </span>

        <div data-ticket-picker data-max="{{ $maxQty }}">
            @foreach ($groups as $status => $categories)
                <div class="ticket-group">
                    <span class="pill">{{ $categories->first()->status_label }}</span>

                    @foreach ($categories as $cat)
                        <article class="ticket" data-category data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-price="{{ $cat->price }}">
                            <div class="ticket__top">
                                <h3>{{ $cat->name }}</h3>
                                @if ($cat->is_limited)
                                    <span class="badge">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2M9 2h6M12 2v3"/></svg>
                                        Waktu Terbatas
                                    </span>
                                @endif
                            </div>

                            <div class="ticket__bottom">
                                <strong class="ticket__price">{{ $cat->price_label }}</strong>

                                @if ($cat->status === 'on_sale')
                                    <button type="button" class="btn btn--primary btn--sm" data-pick>Pilih</button>

                                    <div class="qty" data-stepper hidden>
                                        <button type="button" class="qty__btn" data-minus aria-label="Kurangi">&minus;</button>
                                        <span class="qty__num" data-qty aria-live="polite">0</span>
                                        <button type="button" class="qty__btn" data-plus aria-label="Tambah">+</button>
                                    </div>
                                @else
                                    <span class="muted">{{ $cat->status_label }}</span>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <aside class="checkout__side">
        <form method="POST" action="{{ route('checkout.store', $event) }}" class="panel">
            @csrf

            {{-- Belum ada tiket dipilih --}}
            <div id="summary-idle">
                <div class="box">
                    <div class="row"><span class="muted">Harga mulai dari</span><strong>{{ $event->price_label }}</strong></div>
                    <button type="button" class="btn btn--primary btn--block" disabled>Beli Sekarang</button>
                </div>
            </div>

            {{-- Sudah ada tiket dipilih --}}
            <div id="summary-filled" hidden>
                <h2 class="panel__title">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 8V7a5 5 0 0 1 10 0v1h2.2l.8 12.2A1.5 1.5 0 0 1 18.5 22h-13A1.5 1.5 0 0 1 4 20.2L4.8 8H7Zm2 0h6V7a3 3 0 0 0-6 0v1Z"/></svg>
                    Rincian Pesanan
                </h2>
                <div class="box">
                    <div id="summary-lines"></div>
                    <div class="row row--line"><span class="muted">Subtotal</span><span id="summary-subtotal"></span></div>
                    <div class="row row--line"><span id="summary-count"></span><strong id="summary-total" class="total"></strong></div>
                </div>
                <button type="submit" class="btn btn--primary btn--block btn--solo">Beli Sekarang</button>
            </div>

            <div id="summary-inputs"></div>

            @error('items')
                <p class="error">{{ $message }}</p>
            @enderror
        </form>
    </aside>
</div>
<script>
(() => {
    const root = document.querySelector('[data-ticket-picker]');
    if (!root) return;

    const max = parseInt(root.dataset.max, 10) || 5;
    const rupiah = (n) => 'Rp' + Number(n).toLocaleString('id-ID');
    const cards = [...root.querySelectorAll('[data-category]')];
    const qty = new Map();

    const idle = document.getElementById('summary-idle');
    const filled = document.getElementById('summary-filled');
    const lines = document.getElementById('summary-lines');
    const inputs = document.getElementById('summary-inputs');
    const subtotal = document.getElementById('summary-subtotal');
    const count = document.getElementById('summary-count');
    const totalEl = document.getElementById('summary-total');

    const el = (tag, cls, text) => {
        const node = document.createElement(tag);
        if (cls) node.className = cls;
        if (text !== undefined) node.textContent = text;
        return node;
    };

    const hidden = (name, value) => {
        const i = document.createElement('input');
        i.type = 'hidden';
        i.name = name;
        i.value = value;
        return i;
    };

    const render = () => {
        let totalQty = 0;
        let totalPrice = 0;
        let row = 0;
        lines.replaceChildren();
        inputs.replaceChildren();

        cards.forEach((card) => {
            const id = card.dataset.id;
            const q = qty.get(id) || 0;

            const pick = card.querySelector('[data-pick]');
            const stepper = card.querySelector('[data-stepper]');
            if (!pick || !stepper) return;

            pick.hidden = q > 0;
            stepper.hidden = q === 0;
            card.querySelector('[data-qty]').textContent = q;
            card.querySelector('[data-plus]').disabled = q >= max;

            if (q > 0) {
                const price = Number(card.dataset.price);
                totalQty += q;
                totalPrice += q * price;

                const line = el('div', 'line');
                const left = el('div');
                left.append(el('div', '', card.dataset.name), el('div', 'muted small', 'x' + q));
                line.append(left, el('span', '', rupiah(q * price)));
                lines.append(line);

                inputs.append(hidden(`items[${row}][category_id]`, id), hidden(`items[${row}][qty]`, q));
                row++;
            }
        });

        idle.hidden = totalQty > 0;
        filled.hidden = totalQty === 0;
        subtotal.textContent = rupiah(totalPrice);
        count.textContent = `Total ${totalQty} Item`;
        totalEl.textContent = rupiah(totalPrice);
    };

    root.addEventListener('click', (e) => {
        const btn = e.target.closest('button');
        const card = btn && btn.closest('[data-category]');
        if (!card) return;

        const id = card.dataset.id;
        const current = qty.get(id) || 0;

        if (btn.hasAttribute('data-pick')) qty.set(id, 1);
        else if (btn.hasAttribute('data-plus')) qty.set(id, Math.min(max, current + 1));
        else if (btn.hasAttribute('data-minus')) qty.set(id, Math.max(0, current - 1));
        else return;

        render();
    });

    render();
})();
</script>
@endsection