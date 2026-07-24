<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/** 永続化 port（実装は Infrastructure）。 */
interface SeatInventoryRepository
{
    public function find(PerformanceId $performanceId, SeatNo $seatNo): ?SeatInventory;

    /** @return list<SeatInventory> */
    public function findAllByPerformance(PerformanceId $performanceId): array;

    public function save(SeatInventory $inventory): void;
}
