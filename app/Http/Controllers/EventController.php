<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketCategory;

class EventController extends Controller
{
    public function show(Event $event)
    {
        $event->load(['organizer', 'ticketCategories']);

        $groups = $event->ticketCategories->groupBy('status');
        $maxQty = TicketCategory::MAX_QTY;

        return view('events.show', compact('event', 'groups', 'maxQty'));
    }
}
