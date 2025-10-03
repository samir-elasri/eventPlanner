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
        return $this->eventRepository->all();
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
        return $this->eventRepository->destroy($request->event_id);
    }

    /**
     * @OA\Put(
     *     path="/api/dashboard/events/{event_id}/toggle-role",
     *     summary="Toggle event role",
     *     @OA\Response(response=200, description="Event role toggled successfully")
     * )
     */
    public function toggleRole(Request $request)
    {
        return $this->eventRepository->destroy($request->event_id);
    }
}
