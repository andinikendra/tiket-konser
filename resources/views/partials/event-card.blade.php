<a href="{{ route('events.show', $event) }}" class="event-card"
   data-search="{{ \Illuminate\Support\Str::lower($event->title . ' ' . $event->organizer->name) }}"
   data-price="{{ $event->starting_price }}"
   data-date="{{ $event->start_date->format('Ymd') }}">

    <div class="poster" style="background: {{ $event->poster_gradient }}">
        @if ($event->poster_url)
            <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" loading="lazy">
        @else
            <span class="poster__title">{{ $event->title }}</span>
        @endif
    </div>

    <div class="event-card__body">
        <h3 class="event-card__title">{{ $event->title }}</h3>
        <p class="event-card__meta">{{ $event->date_label }}</p>
        <p class="event-card__meta">{{ $event->organizer->name }}</p>
        <p class="event-card__price">{{ $event->price_label }}</p>
    </div>
</a>