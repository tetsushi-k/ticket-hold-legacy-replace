<?php

declare(strict_types=1);

namespace TicketHold\Application;

use TicketHold\Domain\BuyerId;
use TicketHold\Domain\OperationResult;
use TicketHold\Domain\PerformanceId;
use TicketHold\Domain\RejectionReason;
use TicketHold\Domain\SeatInventoryRepository;
use TicketHold\Domain\SeatNo;

final class HoldSeatUseCase
{
    public function __construct(
        private SeatInventoryRepository $repository,
        private Clock $clock,
    ) {
    }

    public function execute(string $performanceId, string $seatNo, string $buyerId): OperationResult
    {
        $inventory = $this->repository->find(
            new PerformanceId($performanceId),
            new SeatNo($seatNo),
        );
        if ($inventory === null) {
            return OperationResult::rejected(RejectionReason::SeatNotFound);
        }

        $result = $inventory->hold(new BuyerId($buyerId), $this->clock->now());
        if ($result->isOk()) {
            $this->repository->save($inventory);
        }

        return $result;
    }
}
