<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            "id" => ['required'],
            "adult_ticket_count" => ['required'],
            "kid_ticket_count" => ['required'],
            "ticket_count_*" => ['nullable', 'integer'],
        ]);

        $event = Event::find(request('id'));
        $event->prices = json_decode($event->prices);

        // Save ticket types
        $ticketTypes = [];

        foreach ($request->all() as $key => $value) {
            if (preg_match('/^ticket_count_(\d+)$/', $key, $matches)) {
                $ticketTypeId = $matches[1];
                $ticketCount = (int)$value;

                $eventType = $event->types()->where('event_type_id', $ticketTypeId)->first();

                if ($eventType) {
                    $ticketTypes[$eventType->name] = [
                        'id' => $eventType->id,
                        'count' => $ticketCount,
                        'price' => json_decode($eventType->pivot->prices)
                    ];
                }
            }
        }

        $order = Order::create([
            'event_id' => $event->id,
            'event_date' => $event->date,
            'ticket_adult_price' => $event->prices->adult,
            'ticket_adult_quantity' => $request->get('adult_ticket_count'),
            'ticket_kid_price' => $event->prices->kid,
            'ticket_kid_quantity' => $request->get('kid_ticket_count'),

            'ticket_benefits_price' => $ticketTypes['Льготный']['price']->price ?? null,
            'ticket_benefits_quantity' => $ticketTypes['Льготный']['count'] ?? null,
            'ticket_command_price' => $ticketTypes['Коммандный']['price']->price ?? null,
            'ticket_command_quantity' => $ticketTypes['Коммандный']['count'] ?? null,

            'barcode' => $request->get('barcode'),
            'user_id' => Auth::user()->id,
            'equal_price' => empty($ticketTypes) ? $this->countSum($event, $request) : $this->countSum($event, $request, $ticketTypes)
        ]);

        $this->makeTickets($order);

        return redirect('/');
    }

    protected function countSum(Event $event, Request $request, $ticketTypes = []): int
    {
        if (empty($ticketTypes)) {
            return (+$event->prices->adult * $request->get('adult_ticket_count')) +
                (+$event->prices->kid * $request->get('kid_ticket_count'));
        } else {
            $sum = 0;
            foreach ($ticketTypes as $type) {
                $sum += +$type['count'] * +$type['price']->price;
            }

            return ((+$event->prices->adult * $request->get('adult_ticket_count')) +
                (+$event->prices->kid * $request->get('kid_ticket_count')) + $sum);
        };
    }

    protected function makeTickets(Order $order)
    {
        $attributes = $order->getAttributes();
        // достаем все заказы, убираем с null
        $ticketProperties = array_filter($attributes, function($key) use ($attributes) {
            return strpos($key, 'ticket_') === 0 && $attributes[$key] !== null;
        }, ARRAY_FILTER_USE_KEY);

        $string = "ticket_adult_price";
        $result = explode('_', $string)[1];

        $count = 0;
        foreach ($ticketProperties as $key => $value) {
            if (strpos($key, '_quantity') !== false) {
                $value = (int)$value;
                $name= explode('_', $key)[1];

                for ($i = 0; $i < $value; $i++) {
                    Ticket::create([
                        'order_id' => $order->id,
                        'event_id' => $order->event_id,
                        'event_date' => $order->event_date,
                        'type' => $name,
                        'price' => $ticketProperties['ticket_' . $name . '_price'],
                        'barcode' => rand(10000000, 99999999),
                    ]);
                }

            }
        }

    }

    protected function getCountTickets(Order $order): int
    {
        $count = 0;

        $count = $order->ticket_adult_quantity + $order->ticket_kid_quantity + $order->ticket_benefits_quantity + $order->ticket_command_quantity;

        return $count;
    }
}
