<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class EventRegistrationConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $registration;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, Registration $registration)
    {
        $this->event = $event;
        $this->registration = $registration;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $eventDate = Carbon::parse($this->event->start_datetime);
        $isWaitlisted = $this->registration->status === 'waitlist';

        $mailMessage = (new MailMessage)
            ->subject($isWaitlisted ? 'Waitlist Confirmation - ' . $this->event->name : 'Event Registration Confirmed - ' . $this->event->name)
            ->greeting('Hello ' . $notifiable->name . '!');

        if ($isWaitlisted) {
            $mailMessage->line('You have been added to the waitlist for the following event:');
        } else {
            $mailMessage->line('Your registration for the following event has been confirmed:');
        }

        $mailMessage
            ->line('**Event:** ' . $this->event->name)
            ->line('**Date:** ' . $eventDate->format('F j, Y'))
            ->line('**Time:** ' . $eventDate->format('g:i A'))
            ->line('**Duration:** ' . $this->event->duration . ' minutes')
            ->line('**Location:** ' . $this->event->location);

        if ($this->event->description) {
            $mailMessage->line('**Description:** ' . $this->event->description);
        }

        if ($isWaitlisted) {
            $mailMessage->line('You are currently on the waitlist. We will notify you if a spot becomes available.');
        } else {
            $mailMessage->line('We look forward to seeing you at the event!');
        }

        $mailMessage->line('Thank you for registering!');

        return $mailMessage;
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
            'registration_status' => $this->registration->status,
            'message' => $this->registration->status === 'waitlist' 
                ? 'You have been added to the waitlist for ' . $this->event->name
                : 'Your registration for ' . $this->event->name . ' has been confirmed',
        ];
    }
}
