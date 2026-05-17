<?php

namespace App\Events;

use App\Models\JamaahBooking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingFullyPaid
{
    use Dispatchable, SerializesModels;

    public $booking;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\JamaahBooking  $booking
     * @return void
     */
    public function __construct(JamaahBooking $booking)
    {
        $this->booking = $booking;
    }
}
