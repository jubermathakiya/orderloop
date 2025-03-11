<?php

namespace App\Listeners;

use App\Events\BookReturned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBookReturnNotification
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
    public function handle(BookReturned $event): void
    {
        Log::info("Book '{$event->book->title}' received by {$event->user->name}.");

    }
}
