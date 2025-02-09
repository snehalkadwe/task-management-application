<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\SendTaskCompletedNotificationToUser;

class SendTaskCompletedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCompleted $event): void
    {
        $event->task->user->notify(new SendTaskCompletedNotificationToUser($event->task));
    }
}
