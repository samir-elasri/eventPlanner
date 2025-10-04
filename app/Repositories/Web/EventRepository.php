<?php

namespace App\Repositories\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Web\Event\EventsResource;
use Illuminate\Support\Facades\Auth;

class EventRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $query = Event::query();

            if ($user && $user->hasRole('admin')) {
                $events = $query->get();
            } elseif ($user && $user->hasRole('user')) {
                $events = $query->where('status', 'live')->get();
            } else {
                $events = $query->where('status', 'live')->get();
            }

            return [
                'events' => EventsResource::collection($events)
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
