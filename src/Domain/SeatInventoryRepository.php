<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/** 永続化 port（実装は Infrastructure）。 */
interface SeatInventoryRepository
{
    public function find(PerformanceId $performanceId, SeatNo $seatNo): ?SeatInventory;

    public function save(SeatInventory $inventory): void;
}
