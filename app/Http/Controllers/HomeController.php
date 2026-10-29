<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Adventure;
use App\Models\Darshan;
use App\Models\Destination;
use App\Models\Event;
use App\Models\FoodExperience;
use App\Models\Hotel;
use App\Models\Pooja;
use App\Models\Restaurant;
use App\Models\Testimonial;
use App\Models\Trip;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        try {
            $destinations = Destination::where('is_active', true)->where('is_featured', true)->take(6)->get();
            if ($destinations->isEmpty()) {
                $destinations = Destination::where('is_active', true)->take(6)->get();
            }
            $hotels = Hotel::where('is_active', true)->with('destination')->take(4)->get();
            if ($hotels->isEmpty()) {
                $hotels = Hotel::with('destination')->take(4)->get();
            }
            $adventures = Adventure::where('is_active', true)->with('destination')->take(4)->get();
            $events = Event::where('is_active', true)->take(4)->get();
            $darshans = Darshan::where('is_active', true)->with('destination')->take(4)->get();
            $poojas = Pooja::where('is_active', true)->with('destination')->take(4)->get();
            $restaurants = Restaurant::where('is_active', true)->with('destination')->orderByDesc('rating')->take(4)->get();
            $experiences = FoodExperience::where('is_active', true)->with(['destination', 'restaurant'])->take(4)->get();

            $trips = Trip::with('user', 'destination')
                ->where('is_public', true)
                ->latest()
                ->take(3)
                ->get();

            $testimonials = Testimonial::where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->take(6)
                ->get();

            $counts = [
                'destinations' => Destination::where('is_active', true)->count(),
                'hotels' => Hotel::where('is_active', true)->count(),
                'events' => Event::where('is_active', true)->count(),
                'adventures' => Adventure::where('is_active', true)->count(),
                'darshan' => Darshan::where('is_active', true)->count(),
                'pooja' => Pooja::where('is_active', true)->count(),
                'restaurants' => Restaurant::where('is_active', true)->count(),
            ];

            $destinationOptions = Destination::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);
        } catch (\Throwable) {
            $destinations = collect();
            $hotels = collect();
            $adventures = collect();
            $events = collect();
            $darshans = collect();
            $poojas = collect();
            $restaurants = collect();
            $experiences = collect();
            $trips = collect();
            $testimonials = collect();
            $counts = [
                'destinations' => 0,
                'hotels' => 0,
                'events' => 0,
                'adventures' => 0,
                'darshan' => 0,
                'pooja' => 0,
                'restaurants' => 0,
            ];
            $destinationOptions = collect();
        }

        return Inertia::render('Home', [
            'destinations' => $destinations,
            'hotels' => $hotels,
            'adventures' => $adventures,
            'events' => $events,
            'darshans' => $darshans,
            'poojas' => $poojas,
            'restaurants' => $restaurants,
            'experiences' => $experiences,
            'trips' => $trips,
            'testimonials' => $testimonials,
            'counts' => $counts,
            'destinationOptions' => $destinationOptions,
        ]);
    }
}
