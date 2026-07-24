<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/** コマンド結果。拒否は例外ではなく rejected + 理由。 */
final readonly class OperationResult
{
    private function __construct(
        private bool $ok,
        private ?RejectionReason $reason = null,
    ) {
    }

    public static function ok(): self
    {
        return new self(true);
    }

    public static function rejected(RejectionReason $reason): self
    {
        return new self(false, $reason);
    }

    public function isOk(): bool
    {
        return $this->ok;
    }

    public function rejectionReason(): ?RejectionReason
    {
        return $this->reason;
    }
}
