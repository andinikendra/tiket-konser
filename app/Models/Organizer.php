<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizer extends Model
{
    protected $fillable = ['name', 'logo'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function getInitialAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->name, 0, 1));
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo && file_exists(public_path($this->logo)) ? asset($this->logo) : null;
    }
}
