<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/** コマンド結果。拒否は例外ではなく false（受入例示の「拒否」）。 */
final readonly class OperationResult
{
    private function __construct(private bool $ok)
    {
    }

    public static function ok(): self
    {
        return new self(true);
    }

    public static function rejected(): self
    {
        return new self(false);
    }

    public function isOk(): bool
    {
        return $this->ok;
    }
}
