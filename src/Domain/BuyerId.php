<?php

declare(strict_types=1);

namespace TicketHold\Domain;

final readonly class BuyerId
{
    public function __construct(public string $value)
    {
        if ($value === '') {
            throw new \InvalidArgumentException('BuyerId must not be empty');
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
