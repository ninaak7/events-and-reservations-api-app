<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $name = $request->input('name');
        $location = $request->input('location');

        $events = Event::query()
            ->when($name, fn (Builder $builder) => $builder->where('name', 'like', '%'.$name.'%'))
            ->when($location, fn (Builder $builder) => $builder->where('location', 'like', '%'.$location.'%'))
            ->paginate();

        return EventResource::collection($events);
    }

    public function store(EventRequest $request): JsonResource
    {
        $event = Event::query()->create($request->validated());

        return JsonResource::make([
            'message' => 'Event created successfully',
        ]);
    }

    public function update(EventRequest $request, Event $event): JsonResource
    {
        $event->update($request->validated());

        return JsonResource::make([
            'message' => 'Event updated successfully',
        ]);
    }

    public function show(Event $event): EventResource
    {
        $event->loadMissing('reservations');

        return EventResource::make($event);
    }

    public function destroy(Event $event): JsonResource
    {
        $confirmedReservations = Reservation::query()
            ->where('event_id', $event->id)
            ->where('status', ReservationStatus::CONFIRMED)
            ->count();

        abort_if($confirmedReservations !== 0, 400);

        $event->delete();

        return JsonResource::make([
            'message' => 'Event deleted successfully',
        ]);
    }
}
