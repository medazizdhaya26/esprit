<?php

namespace App\Message;

Class NewReservationNotificationMessage
{
    private int $ReservationId;

    public function __construct(int $ReservationId)
    {
        $this->ReservationId = $ReservationId;
    }

    public function getReservationId(): int
    {
        return $this->ReservationId;
    }

}