<?php

namespace App\Listeners;

use App\Events\EventTrigger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EventTrigger  $event
     * @return void
     */
    public function handle(EventTrigger $event)
    {
        //
    }

}
