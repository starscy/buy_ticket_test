<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Сервис по работе с заказами
 */
class OrderService
{
    private Order $order;

    /**
     * Создание заказа
     *
     * @param $request
     * @return Order
     */
    public function createOrder($request): Order
    {
        $event = Event::find(request('id'));
        $event->prices = json_decode($event->prices);

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

        $this->order = Order::create([
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

        return $this->order;
    }

    /**
     * Создание билетов
     *
     * @return void
     */
    public function makeTickets(): void
    {
        $attributes = $this->order->getAttributes();
        // достаем все заказы, убираем с null
        $ticketProperties = array_filter($attributes, function($key) use ($attributes) {
            return str_starts_with($key, 'ticket_') && $attributes[$key] !== null;
        }, ARRAY_FILTER_USE_KEY);

        foreach ($ticketProperties as $key => $value) {
            if (str_contains($key, '_quantity')) {
                $value = (int)$value;
                $name= explode('_', $key)[1];

                for ($i = 0; $i < $value; $i++) {
                    Ticket::create([
                        'order_id' => $this->order->id,
                        'event_id' => $this->order->event_id,
                        'event_date' => $this->order->event_date,
                        'type' => $name,
                        'price' => $ticketProperties['ticket_' . $name . '_price'],
                        'barcode' => rand(10000000, 99999999),
                    ]);
                }
            }
        }
    }

    /**
     * Подсчет суммы
     *
     * @param Event $event
     * @param Request $request
     * @param $ticketTypes
     * @return int
     */
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
}
