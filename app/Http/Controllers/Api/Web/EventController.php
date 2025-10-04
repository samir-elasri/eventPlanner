<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Web\Event\EventsResource;
use App\Repositories\Web\EventRepository;

class EventController extends Controller
{
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/web/events",
     *     summary="Get all events",
     *     @OA\Response(response=200, description="Events retrieved successfully")
     * )
     */
    public function index()
    {
        $this->authorize('viewAny', Event::class);
        return $this->eventRepository->index(['status' => 'live']);
    }
}
