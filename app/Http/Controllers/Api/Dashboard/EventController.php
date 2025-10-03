<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Dashboard\Event\EventsResource;
use App\Http\Resources\Dashboard\Event\EventResource;

class EventController extends Controller
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

    /**
     * Create a newly created resource in storage.
     */
    public function create(Request $request)
    {
        try {
            $event = Event::create([
                "name" => $request->input('name'),
                "start_datetime" => $request->input('start_datetime'),
                "duration" => $request->input('duration'),
                "description" => $request->input('description'),
                "location" => $request->input('location'),
                "capacity" => $request->input('capacity'),
                "waitlist_capacity" => $request->input('waitlist_capacity'),
                "status" => $request->input('status', 'pending')
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'Event created successfully',
                'data' => new EventResource($event)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
            'status' => 500,
            'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($event_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            return [
                'data' => new EventResource($event)
            ];
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Event Event Not Found!";
            return [
                'error' => $errorMessage,
                'status' => 404
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $event = Event::findOrFail($request->event_id);

            $event->update($request->only([
                'name',
                'start_datetime',
                'duration',
                'description',
                'location',
                'capacity',
                'waitlist_capacity',
                'status'
            ]));

            return response()->json([
                'status' => 200,
                'message' => 'Event updated successfully',
                'data' => new EventResource($event)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($event_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            $event->delete();
            return response()->json([
                'status' => 200,
                'message' => "Event deleted successfully",
            ], 200);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Event not found!";
            return response()->json([
                'status' => 404,
                'message' => $errorMessage,
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Toggle status between 'live' and 'draft'
    public function toggleStatus($event_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            $event->status = ($event->status === 'live') ? 'draft' :
            $event->save();
            return response()->json([
                'status' => 200,
                'message' => "Event status toggled successfully",
                'data' => new EventResource($event)
            ], 200);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Event not found!";
            return response()->json([
                'status' => 404,
                'message' => $errorMessage,
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
