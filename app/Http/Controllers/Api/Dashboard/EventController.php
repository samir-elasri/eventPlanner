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

    public function index()
    {
        return $this->eventRepository->all();
    }

    public function create(Request $request)
    {
        $params = $request->all();
        return $this->eventRepository->create($params);
    }

    public function show(Request $request)
    {
        return $this->eventRepository->show($request->event_id);
    }

    public function update(Request $request)
    {
        $params = $request->all();
        return $this->eventRepository->create($params, $request->event_id);
    }

    public function destroy(Request $request)
    {
        return $this->eventRepository->destroy($request->event_id);
    }

    public function toggleRole(Request $request)
    {
        return $this->eventRepository->destroy($request->event_id);
    }
}
