<?php

declare(strict_types=1);

namespace TicketHold\Application;

use TicketHold\Domain\PerformanceId;
use TicketHold\Domain\SeatInventoryRepository;

final class ReleaseExpiredSeatsUseCase
{
    public function __construct(
        private SeatInventoryRepository $repository,
        private Clock $clock,
    ) {
    }

    public function execute(string $performanceId): int
    {
        $released = 0;
        foreach ($this->repository->findAllByPerformance(new PerformanceId($performanceId)) as $inventory) {
            $result = $inventory->releaseExpired($this->clock->now());
            if ($result->isOk()) {
                $this->repository->save($inventory);
                ++$released;
            }
        }

        return $released;
    }
}
