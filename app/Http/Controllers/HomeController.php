<?php

namespace App\Http\Controllers;

use App\Models\Adventure;
use App\Models\Darshan;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\Pooja;
use App\Models\Trip;

class HomeController extends Controller
{
    public function index()
    {
        $destinations = Destination::where('is_active', true)->where('is_featured', true)->take(6)->get();
        $hotels = Hotel::where('is_active', true)->with('destination')->take(4)->get();
        $adventures = Adventure::where('is_active', true)->with('destination')->take(4)->get();
        $events = Event::where('is_active', true)->take(4)->get();
        $darshans = Darshan::where('is_active', true)->with('destination')->take(4)->get();
        $poojas = Pooja::where('is_active', true)->with('destination')->take(4)->get();

        $trips = Trip::with('user', 'destination')
            ->where('is_public', true)
            ->latest()
            ->take(3)
            ->get();

        $counts = [
            'destinations' => Destination::where('is_active', true)->count(),
            'hotels' => Hotel::where('is_active', true)->count(),
            'events' => Event::where('is_active', true)->count(),
            'adventures' => Adventure::where('is_active', true)->count(),
            'darshan' => Darshan::where('is_active', true)->count(),
            'pooja' => Pooja::where('is_active', true)->count(),
        ];

        $destinationOptions = Destination::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('home', compact(
            'destinations',
            'hotels',
            'adventures',
            'events',
            'darshans',
            'poojas',
            'trips',
            'counts',
            'destinationOptions'
        ));
    }
}
