<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Домашняя страница
 */
class HomeController extends Controller
{
    public function index(): Response
    {
        $events = Event::with('types')->get();

        foreach ($events as $event) {
            $event['prices'] = json_decode( $event['prices']);
            $event['types'] = $event->types->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'prices' => json_decode($type->pivot->prices),
                ];
            });
        }

        return Inertia::render('Home', [
            "events" => $events
        ]);
    }
}
