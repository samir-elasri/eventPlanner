<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_datetime', 
        'duration',
        'description',
        'location',
        'capacity',
        'waitlist_capacity',
        'status'
    ];
    
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function joinedRegistrations()
    {
        return $this->registrations()->where('status', 'joined');
    }

    public function waitlistedRegistrations()
    {
        return $this->registrations()->where('status', 'waitlist');
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Registration::class);
    }

        /**
     * Get the end datetime of the event
     */
    public function getEndDatetimeAttribute()
    {
        return Carbon::parse($this->start_datetime)->addMinutes($this->duration);
    }

    /**
     * Check if this event overlaps with another event
     */
    public function overlapsWith(Event $otherEvent)
    {
        $thisStart = Carbon::parse($this->start_datetime);
        $thisEnd = $this->end_datetime;
        $otherStart = Carbon::parse($otherEvent->start_datetime);
        $otherEnd = $otherEvent->end_datetime;

        // Events overlap if one starts before the other ends
        return $thisStart->lt($otherEnd) && $otherStart->lt($thisEnd);
    }

    /**
     * Get events that overlap with this event for a specific user
     */
    public static function getOverlappingEventsForUser($userId, $eventToCheck)
    {
        // Get all events the user is registered for (joined status only)
        $userEvents = Event::whereHas('registrations', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'joined'); // Only check joined events, not waitlisted
        })->get();

        $overlappingEvents = [];
        foreach ($userEvents as $event) {
            if ($event->id !== $eventToCheck->id && $event->overlapsWith($eventToCheck)) {
                $overlappingEvents[] = $event;
            }
        }

        return $overlappingEvents;
    }

    /**
     * Check if the event is full (capacity reached)
     */
    public function isFull()
    {
        return $this->joinedRegistrations()->count() >= $this->capacity;
    }

    /**
     * Check if the waitlist is full
     */
    public function isWaitlistFull()
    {
        return $this->waitlistedRegistrations()->count() >= $this->waitlist_capacity;
    }

    /**
     * Get available spots in the event
     */
    public function getAvailableSpots()
    {
        return max(0, $this->capacity - $this->joinedRegistrations()->count());
    }

    /**
     * Get available waitlist spots
     */
    public function getAvailableWaitlistSpots()
    {
        return max(0, $this->waitlist_capacity - $this->waitlistedRegistrations()->count());
    }

    /**
     * Check if a user can join the event
     * Returns: ['can_join' => bool, 'status' => 'joined'|'waitlist'|'full', 'message' => string]
     */
    public function canUserJoin($userId)
    {
        // Check if user already registered
        $existingRegistration = $this->registrations()->where('user_id', $userId)->first();
        if ($existingRegistration) {
            return [
                'can_join' => false,
                'status' => 'already_registered',
                'message' => 'You are already registered for this event.'
            ];
        }

        // Check if event is full
        if (!$this->isFull()) {
            return [
                'can_join' => true,
                'status' => 'joined',
                'message' => 'You can join this event.'
            ];
        }

        // Event is full, check waitlist
        if (!$this->isWaitlistFull()) {
            return [
                'can_join' => true,
                'status' => 'waitlist',
                'message' => 'Event is full. You will be added to the waitlist.'
            ];
        }

        // Both event and waitlist are full
        return [
            'can_join' => false,
            'status' => 'full',
            'message' => 'Event and waitlist are both full.'
        ];
    }
}
