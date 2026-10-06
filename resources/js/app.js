// Pilih tiket: tombol "Pilih" -> stepper (-/+), ringkasan di sisi kanan ikut berubah.
// Harga hanya untuk tampilan; server menghitung ulang dari database saat checkout.
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

            card.querySelector('[data-pick]').hidden = q > 0;
            card.querySelector('[data-stepper]').hidden = q === 0;
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
