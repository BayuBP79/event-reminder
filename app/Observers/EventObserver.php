<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\Event;

class EventObserver
{
    public function created(Event $event)
    {
        $reminderDate = Carbon::parse($event->event_date)->subMinutes(30);

        $event->reminders()->create([
            'reminder_date' => $reminderDate,
            'message' => 'Reminder: ' . $event->title . ' is starting soon!'
        ]);
    }
}
