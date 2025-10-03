<?php

namespace App\Repositories\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Web\Event\EventsResource;

class EventRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $events =  Event::paginate(15);
            $data = [
                'events' => EventsResource::collection($events),
                'pagination' => [
                    'total' => $events->total(),
                    'per_page' => $events->perPage(),
                    'current_page' => $events->currentPage(),
                    'total_pages' => $events->lastPage(),
                    'next_page_url' => $events->nextPageUrl(),
                    'prev_page_url' => $events->previousPageUrl()
                ]
            ];
            return $data;
        } catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
}
