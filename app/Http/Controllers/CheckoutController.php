<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Alur checkout disimpan di session (belum ada tabel orders).
 * Harga SELALU dihitung ulang dari database, bukan dari input browser.
 */
class CheckoutController extends Controller
{
    public const METHODS = [
        'va'      => 'Virtual Account (BCA, Mandiri, BNI, BRI)',
        'qris'    => 'QRIS',
        'ewallet' => 'E-Wallet (GoPay, OVO, DANA, ShopeePay)',
    ];

    public function store(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'items'               => ['required', 'array', 'min:1'],
            'items.*.category_id' => ['required', 'integer'],
            'items.*.qty'         => ['required', 'integer', 'min:1', 'max:' . TicketCategory::MAX_QTY],
        ], ['items.required' => 'Pilih minimal satu tiket.']);

        $categories = $event->ticketCategories()
            ->where('status', 'on_sale')
            ->whereIn('id', collect($data['items'])->pluck('category_id'))
            ->get()->keyBy('id');

        $items = [];
        foreach ($data['items'] as $row) {
            if ($cat = $categories->get($row['category_id'])) {
                $items[] = [
                    'category_id' => $cat->id,
                    'name'        => $cat->name,
                    'price'       => $cat->price,
                    'qty'         => (int) $row['qty'],
                ];
            }
        }

        if (! $items) {
            return back()->withErrors(['items' => 'Tiket yang dipilih tidak tersedia.']);
        }

        session([$this->key($event) => ['items' => $items]]);

        return redirect()->route('checkout.detail', $event);
    }

    public function detail(Event $event)
    {
        return $this->render($event, 'checkout.detail');
    }

    public function saveDetail(Request $request, Event $event): RedirectResponse
    {
        $cart = $this->cart($event) ?? abort(404);

        $cart['buyer'] = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ]);
        session([$this->key($event) => $cart]);

        return redirect()->route('checkout.method', $event);
    }

    public function method(Event $event)
    {
        return $this->render($event, 'checkout.method', ['methods' => self::METHODS]);
    }

    public function saveMethod(Request $request, Event $event): RedirectResponse
    {
        $cart = $this->cart($event) ?? abort(404);

        $cart['method'] = $request->validate([
            'method' => ['required', 'in:' . implode(',', array_keys(self::METHODS))],
        ])['method'];
        $cart['order_code'] = $cart['order_code'] ?? 'ATX-' . strtoupper(Str::random(8));
        session([$this->key($event) => $cart]);

        return redirect()->route('checkout.payment', $event);
    }

    public function payment(Event $event)
    {
        return $this->render($event, 'checkout.payment', ['methods' => self::METHODS]);
    }

    // ---------------------------------------------------------------

    private function key(Event $event): string
    {
        return 'checkout.' . $event->id;
    }

    private function cart(Event $event): ?array
    {
        return session($this->key($event));
    }

    private function render(Event $event, string $view, array $extra = [])
    {
        $cart = $this->cart($event);
        if (! $cart) {
            return redirect()->route('events.show', $event);
        }

        $event->load('organizer');
        $items    = collect($cart['items']);
        $totalQty = $items->sum('qty');
        $total    = $items->sum(fn ($i) => $i['price'] * $i['qty']);

        return view($view, array_merge(compact('event', 'cart', 'items', 'totalQty', 'total'), $extra));
    }
}
