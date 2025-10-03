<?php

namespace App\Repositories\Web;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Web\Registration\RegistrationsResource;

class RegistrationRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $registrations =  Registration::paginate(15);
            $data = [
                'registrations' => RegistrationsResource::collection($registrations),
                'pagination' => [
                    'total' => $registrations->total(),
                    'per_page' => $registrations->perPage(),
                    'current_page' => $registrations->currentPage(),
                    'total_pages' => $registrations->lastPage(),
                    'next_page_url' => $registrations->nextPageUrl(),
                    'prev_page_url' => $registrations->previousPageUrl()
                ]
            ];
            return $data;
        } catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * join an event via a registration
     */
    public function join($event_id)
    {
        try {
            $event = Event::findOrFail($event_id);

            // case where i already registered
            $existingRegistration = Registration::where('event_id', $event_id)
                ->where('user_id', auth()->user()->id)
                ->first();

            if ($existingRegistration) {
                return response()->json([
                    'code' => 409,
                    'message' => 'You are already registered for this event!'
                ], 409);
            }

            // case where the user already signed up for an event that overlaps with this one
            $overlappingEvents = Event::getOverlappingEventsForUser(auth()->user()->id, $event);
            
            if (!empty($overlappingEvents)) {
                $overlappingEventNames = [];
                foreach ($overlappingEvents as $e) {
                    $overlappingEventNames[] = $e->name;
                }
                
                return response()->json([
                    'code' => 409,
                    'message' => 'You cannot join this event as it overlaps with: ' . implode(', ', $overlappingEventNames),
                    'overlapping_events' => $overlappingEventNames
                ], 409);
            }

            $joinedCount = $event->joinedRegistrations()->count();
            $waitlistCount = $event->waitlistedRegistrations()->count();

            // case where even the waitlist is full
            $status = 'joined';
            if ($joinedCount >= $event->capacity) {
                if ($waitlistCount >= $event->waitlist_capacity) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Event is full and waitlist capacity has been reached!'
                    ], 400);
                }
                $status = 'waitlist';
            }

            $registration = new Registration();
            $registration->event_id = $event->id;
            $registration->user_id = auth()->user()->id;
            $registration->joined_at = now();
            $registration->status = $status;
            $registration->save();

            $message = $status === 'waitlist' 
                ? 'Added to waitlist successfully!' 
                : 'Event joined successfully!';

            return response()->json([
                'code' => 201,
                'message' => $message,
                'status' => $status
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Event not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * cancel my registration to an event
     */
    public function cancel($registration_id)
    {
        try {
            $registration = Registration::where('id', $registration_id)
                ->where('user_id', auth()->user()->id)
                ->firstOrFail();

            $registration->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Opted-out from event successfully!'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Event registration not found!'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
