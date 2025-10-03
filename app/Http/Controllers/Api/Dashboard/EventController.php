<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Dashboard\Event\EventsResource;
use App\Http\Resources\Dashboard\Event\EventResource;
use App\Repositories\Dashboard\EventRepository;

class EventController extends Controller
{
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/events",
     *     summary="Get all events",
     *     @OA\Response(response=200, description="Events retrieved successfully")
     * )
     */
    public function index()
    {
        $this->authorize('viewAny', Event::class);
        return $this->eventRepository->index();
    }

    /**
     * @OA\Post(
     *     path="/api/dashboard/events",
     *     summary="Create a new event",
     *     @OA\Response(response=201, description="Event created successfully")
     * )
     */
    public function create(Request $request)
    {
        $this->authorize('create', Event::class);
        $params = $request->all();
        return $this->eventRepository->create($params);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/events/{event_id}",
     *     summary="Get event by ID",
     *     @OA\Response(response=200, description="Event retrieved successfully")
     * )
     */
    public function show(Request $request)
    {
        $event = Event::findOrFail($request->event_id);
        $this->authorize('view', $event);
        return $this->eventRepository->show($request->event_id);
    }

    /**
     * @OA\Put(
     *     path="/api/dashboard/events/{event_id}",
     *     summary="Update event",
     *     @OA\Response(response=200, description="Event updated successfully")
     * )
     */
    public function update(Request $request)
    {
        $event = Event::findOrFail($request->event_id);
        $this->authorize('update', $event);
        $params = $request->all();
        return $this->eventRepository->create($params, $request->event_id);
    }

    /**
     * @OA\Delete(
     *     path="/api/dashboard/events/{event_id}",
     *     summary="Delete event",
     *     @OA\Response(response=200, description="Event deleted successfully")
     * )
     */
    public function destroy(Request $request)
    {
        $event = Event::findOrFail($request->event_id);
        $this->authorize('delete', $event);
        return $this->eventRepository->destroy($request->event_id);
    }

    /**
     * @OA\Put(
     *     path="/api/dashboard/events/{event_id}/toggle-status",
     *     summary="Toggle event status",
     *     @OA\Response(response=200, description="Event status toggled successfully")
     * )
     */
    public function toggleStatus(Request $request)
    {
        $event = Event::findOrFail($request->event_id);
        $this->authorize('update', $event);
        return $this->eventRepository->toggleStatus($request->event_id);
    }

    /**
     * @OA\Post(
     *     path="/api/dashboard/events/{event_id}/registrations/{registration_id}/upgrade",
     *     summary="Upgrade waitlisted user forcibly",
     *     @OA\Response(response=200, description="Event status toggled successfully")
     * )
     */
    public function upgradeFromWaitlist(Request $request)
    {
        $event = Event::findOrFail($request->event_id);
        $this->authorize('update', $event);
        return $this->eventRepository->upgradeFromWaitlist($request->event_id, $request->registration_id);
    }
    /**
     * @OA\Post(
     *     path="/api/dashboard/{event_id}/auto-upgrade",
     *     summary="Upgarede most recent waitlisted user if possible",
     *     @OA\Response(response=200, description="Event status toggled successfully")
     * )
     */
    public function autoUpgradeFromWaitlist(Request $request)
    {
        $event = Event::findOrFail($request->event_id);
        $this->authorize('update', $event);
        return $this->eventRepository->autoUpgradeFromWaitlist($request->event_id);
    }
}
