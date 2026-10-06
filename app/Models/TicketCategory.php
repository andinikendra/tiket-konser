<?php

namespace App\Models;

use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCategory extends Model
{
    /** Batas beli per kategori (asumsi, ubah sesuai kebutuhan). */
    public const MAX_QTY = 5;

    protected $fillable = ['event_id', 'name', 'price', 'status', 'is_limited'];

    protected $casts = [
        'price'      => 'integer',
        'is_limited' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getPriceLabelAttribute(): string
    {
        return Rupiah::format($this->price);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'on_sale'  => 'On Sale',
            'upcoming' => 'Segera Hadir',
            default    => 'Sold Out',
        };
    }
}
