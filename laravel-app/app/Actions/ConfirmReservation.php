<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Models\Event;
use App\Models\Reservation;

class ConfirmReservation
{
    public function execute(Reservation $reservation): void
    {
        abort_if($reservation->status !== ReservationStatus::PENDING, 400);

        $event = Event::find($reservation->event_id);
        $newCapacity = $event->capacity - $reservation->ticket_quantity;

        $event->update(['capacity' => $newCapacity]);
        $reservation->update(['status' => ReservationStatus::CONFIRMED]);
    }
}
