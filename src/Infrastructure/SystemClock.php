<?php

declare(strict_types=1);

namespace TicketHold\Infrastructure;

use TicketHold\Application\Clock;

final class SystemClock implements Clock
{
    public function __construct(private \DateTimeZone $timeZone)
    {
    }

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', $this->timeZone);
    }
}
