<?php

namespace App\Http\Controllers;

use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $events = Event::with(['organizer', 'ticketCategories'])
            ->orderBy('start_date')
            ->get();

        return view('home.index', compact('events'));
    }
}