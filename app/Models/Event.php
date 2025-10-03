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
}
