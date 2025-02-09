<?php

namespace App\Providers;

use App\Events\TaskCompleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Listeners\SendTaskCompletedNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            TaskCompleted::class,
            SendTaskCompletedNotification::class,
        );
    }
}
