<?php

declare(strict_types=1);

namespace TicketHold\Application;

use TicketHold\Domain\PerformanceId;
use TicketHold\Domain\SeatInventoryRepository;

final readonly class SeatBoardRow
{
    public function __construct(
        public string $seatNo,
        public string $state,
        public ?string $buyerId,
        public ?string $holdUntil,
        public bool $isAvailable,
    ) {
    }
}

final class ListSeatBoardUseCase
{
    public function __construct(
        private SeatInventoryRepository $repository,
        private Clock $clock,
    ) {
    }

    /** @return list<SeatBoardRow> */
    public function execute(string $performanceId): array
    {
        $now = $this->clock->now();
        $rows = [];
        foreach ($this->repository->findAllByPerformance(new PerformanceId($performanceId)) as $inventory) {
            $snapshot = $inventory->snapshot();
            $rows[] = new SeatBoardRow(
                seatNo: $snapshot['seatNo'],
                state: $snapshot['state'],
                buyerId: $snapshot['buyerId'],
                holdUntil: $snapshot['holdUntil'],
                isAvailable: $inventory->isAvailable($now),
            );
        }

        usort($rows, static fn (SeatBoardRow $a, SeatBoardRow $b): int => $a->seatNo <=> $b->seatNo);

        return $rows;
    }
}
