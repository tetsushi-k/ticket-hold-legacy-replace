<?php

declare(strict_types=1);

namespace TicketHold\Application;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
