<?php

namespace App\Listeners;

use App\Events\InboundCreatedEvent;
use App\Events\OutboundCreatedEvent;
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
     * @param  InboundCreatedEvent  $event
     * @return void
     */
    public function handle(InboundCreatedEvent $event)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OutboundCreatedEvent  $event
     * @return void
     */
    public function handle(OutboundCreatedEvent $event)
    {
        //
    }
}
