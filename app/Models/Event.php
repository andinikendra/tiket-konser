<?php

namespace App\Models;

use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'organizer_id', 'title', 'slug', 'poster', 'poster_gradient',
        'start_date', 'end_date', 'location', 'is_popular',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_popular' => 'boolean',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class)->orderBy('price');
    }

    /** "13 Desember 2026" atau "07 - 08 November 2026" */
    public function getDateLabelAttribute(): string
    {
        $s = $this->start_date;
        $e = $this->end_date;

        if (! $e || $s->isSameDay($e)) {
            return $s->translatedFormat('d F Y');
        }
        if ($s->format('Y-m') === $e->format('Y-m')) {
            return $s->translatedFormat('d') . ' - ' . $e->translatedFormat('d F Y');
        }
        return $s->translatedFormat('d M') . ' - ' . $e->translatedFormat('d M Y');
    }

    public function getStartingPriceAttribute(): int
    {
        return (int) ($this->ticketCategories->where('status', 'on_sale')->min('price')
            ?? $this->ticketCategories->min('price')
            ?? 0);
    }

    public function getPriceLabelAttribute(): string
    {
        return Rupiah::format($this->starting_price);
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->poster && file_exists(public_path($this->poster)) ? asset($this->poster) : null;
    }
}
