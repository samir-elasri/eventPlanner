<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Registration;
use App\Notifications\EventReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to users for events happening today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for events happening today...');

        // Get today's date range (start and end of day)
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        // Find all events happening today with 'live' status
        $eventsToday = Event::where('status', 'live')
            ->whereBetween('start_datetime', [$todayStart, $todayEnd])
            ->get();

        if ($eventsToday->isEmpty()) {
            $this->info('No events scheduled for today.');
            return Command::SUCCESS;
        }

        $this->info("Found {$eventsToday->count()} event(s) happening today.");

        $totalNotificationsSent = 0;

        foreach ($eventsToday as $event) {
            $this->line("Processing event: {$event->name}");

            // Get all users who have joined this event (not waitlisted)
            $registrations = Registration::where('event_id', $event->id)
                ->where('status', 'joined')
                ->with('user')
                ->get();

            foreach ($registrations as $registration) {
                try {
                    // Send the notification to the user
                    $registration->user->notify(new EventReminderNotification($event));
                    $totalNotificationsSent++;
                    
                    $this->line("  → Sent reminder to {$registration->user->email}");
                } catch (\Exception $e) {
                    $this->error("  → Failed to send reminder to {$registration->user->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info("\nCompleted! Sent {$totalNotificationsSent} reminder notification(s).");
        
        return Command::SUCCESS;
    }
}
