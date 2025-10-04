<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $eventDate = Carbon::parse($this->event->start_datetime);
        
        return [
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
            'event_start_datetime' => $this->event->start_datetime,
            'event_start_time' => $eventDate->format('g:i A'),
            'event_date' => $eventDate->format('F j, Y'),
            'event_location' => $this->event->location,
            'event_duration' => $this->event->duration,
            'event_description' => $this->event->description,
            'title' => 'Event Reminder',
            'message' => 'You have an event today: ' . $this->event->name . ' at ' . $eventDate->format('g:i A'),
            'type' => 'event_reminder',
        ];
    }
}
