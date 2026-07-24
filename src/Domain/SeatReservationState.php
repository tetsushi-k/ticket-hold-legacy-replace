<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/** Available / OnHold / Confirmed の排他状態。 */
enum SeatReservationState: string
{
    case Available = 'available';
    case OnHold = 'on_hold';
    case Confirmed = 'confirmed';
}
