<?php

namespace App\Repositories\Dashboard;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Dashboard\Event\EventsResource;
use App\Http\Resources\Dashboard\Event\EventResource;

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
                
                $registrations = Registration::where('event_id', $event_id)
                    ->with('user')
                    ->orderBy('status', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->paginate(20);

                return [
                    'event' => new EventResource($event),
                    'registrations' => $registrations->map(function($registration) {
                        return [
                            'id' => $registration->id,
                            'user' => [
                                'id' => $registration->user->id,
                                'name' => $registration->user->name,
                                'email' => $registration->user->email
                            ],
                            'status' => $registration->status,
                            'joined_at' => $registration->joined_at,
                            'created_at' => $registration->created_at
                        ];
                    }),
                    'pagination' => [
                        'total' => $registrations->total(),
                        'per_page' => $registrations->perPage(),
                        'current_page' => $registrations->currentPage(),
                        'total_pages' => $registrations->lastPage()
                    ]
                ];
            } catch (ModelNotFoundException $e) {
                return [
                    'error' => 'Event not found',
                    'status' => 404
                ];
            }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $event_id)
    {
        try {
            $event = Event::findOrFail($event_id);

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
            $event->status = ($event->status === 'live') ? 'draft' : 'live';
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

    // waitlist management

    // upgrade the user from waitlist forcibely
    public function upgradeFromWaitlist($event_id, $registration_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            $registration = Registration::findOrFail($registration_id);

            // base verification
            if ($registration->event_id !== $event->id || $registration->status !== 'waitlist') {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid registration or not on waitlist'
                ], 400);
            }

            // Checking for overlapping events
            $overlappingEvents = Event::getOverlappingEventsForUser($registration->user_id, $event);
            
            if (!empty($overlappingEvents)) {
                $overlappingEventNames = [];
                foreach ($overlappingEvents as $e) {
                    $overlappingEventNames[] = $e->name;
                }
                
                return response()->json([
                    'status' => 409,
                    'message' => 'Cannot upgrade user due to overlapping events: ' . implode(', ', $overlappingEventNames),
                    'overlapping_events' => $overlappingEventNames
                ], 409);
            }

            // Checking if there's seats available
            $joinedCount = $event->joinedRegistrations()->count();
            if ($joinedCount >= $event->capacity) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Event is still at full capacity'
                ], 400);
            }

            $registration->status = 'joined';
            $registration->save();

            return response()->json([
                'status' => 200,
                'message' => 'User upgraded from waitlist successfully',
                'registration' => $registration
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Event or registration not found'
            ], 404);
        }
    }

    // upgrade most recent waitlisted but only if someone canceled already
    public function autoUpgradeFromWaitlist($event_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            $joinedCount = $event->joinedRegistrations()->count();

            if ($joinedCount < $event->capacity) {
                $nextWaitlisted = $event->waitlistedRegistrations()
                    ->with('user')
                    ->orderBy('created_at', 'asc')
                    ->first();

                if ($nextWaitlisted) {
                    $overlappingEvents = Event::getOverlappingEventsForUser($nextWaitlisted->user_id, $event);
                    
                    if (!empty($overlappingEvents)) {
                        $nextWaitlisted = $event->waitlistedRegistrations()
                            ->with('user')
                            ->where('id', '!=', $nextWaitlisted->id)
                            ->orderBy('created_at', 'asc')
                            ->first();
                        
                        // If I find another user without conflicts, upgrade them
                        if ($nextWaitlisted) {
                            $overlappingEvents = Event::getOverlappingEventsForUser($nextWaitlisted->user_id, $event);
                            if (empty($overlappingEvents)) {
                                $nextWaitlisted->status = 'joined';
                                $nextWaitlisted->save();
                                
                                return response()->json([
                                    'status' => 200,
                                    'message' => 'User upgraded from waitlist successfully (skipped conflicting users)',
                                    'upgraded_user' => [
                                        'registration_id' => $nextWaitlisted->id,
                                        'user_name' => $nextWaitlisted->user->name,
                                        'user_email' => $nextWaitlisted->user->email
                                    ]
                                ], 200);
                            }
                        }
                        
                        return response()->json([
                            'status' => 400,
                            'message' => 'No users available for promotion due to scheduling conflicts'
                        ], 400);
                    }
                    
                    $nextWaitlisted->status = 'joined';
                    $nextWaitlisted->save();
                    
                    return response()->json([
                        'status' => 200,
                        'message' => 'User upgraded from waitlist successfully',
                        'upgraded_user' => [
                            'registration_id' => $nextWaitlisted->id,
                            'user_name' => $nextWaitlisted->user->name,
                            'user_email' => $nextWaitlisted->user->email
                        ]
                    ], 200);
                }
            }

            return response()->json([
                'status' => 400,
                'message' => 'No users available for promotion (event may be full or no waitlisted users)'
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Event not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
