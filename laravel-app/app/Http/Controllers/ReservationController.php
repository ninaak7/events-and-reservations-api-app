<?php

namespace App\Http\Controllers;

use App\Actions\CancelReservation;
use App\Actions\ConfirmReservation;
use App\Http\Requests\ReservationRequest;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationController extends Controller
{
    public function store(ReservationRequest $request)
    {
        $event = Event::find($request->event_id);

        $availableCapacity = $event->capacity;
        $ticketsQuantity = $request->ticket_quantity;

        if ($availableCapacity < $ticketsQuantity) {
            return JsonResource::make([
                'error' => true,
                'message' => 'Capacity is exceeded',
            ]);
        }

        Reservation::query()->create($request->validated());

        return JsonResource::make([
            'message' => 'Reservation created successfully',
        ]);
    }

    public function confirmReservation(Reservation $reservation): JsonResource
    {
        (new ConfirmReservation)->execute($reservation);

        return JsonResource::make([
            'message' => 'Reservation successfully confirmed',
        ]);
    }

    public function cancelReservation(Reservation $reservation): JsonResource
    {
        (new CancelReservation)->execute($reservation);

        return JsonResource::make([
            'message' => 'Reservation successfully cancelled',
        ]);
    }
}
