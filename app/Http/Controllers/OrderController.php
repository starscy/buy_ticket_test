<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

/**
 * Заказы
 */
class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    )
    {
    }

    public function store(Request $request): Application|RedirectResponse|Redirector|JsonResponse
    {
        //валидация, можно убрать в Request
        $request->validate([
            "id" => ['required'],
            "adult_ticket_count" => ['required', 'min:0'],
            "kid_ticket_count" => ['required', 'min:0'],
            "ticket_count_*" => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            //создание заказа
            $this->orderService->createOrder($request);

            //создание билетов
            $this->orderService->makeTickets();
        } catch (\Exception $e) {
            return response()->json(['message' => "Ошибка заказа: $e"], 500);
        }

        return redirect('/');
    }
}
