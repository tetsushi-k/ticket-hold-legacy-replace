<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/** コマンド拒否理由（画面・ログ用）。 */
enum RejectionReason: string
{
    case DoubleBooking = 'double_booking';
    case NotOwner = 'not_owner';
    case HoldExpired = 'hold_expired';
    case NoHold = 'no_hold';
    case AlreadyConfirmed = 'already_confirmed';
    case HoldNotExpired = 'hold_not_expired';
    case NotOnHold = 'not_on_hold';
    case SeatNotFound = 'seat_not_found';
}
