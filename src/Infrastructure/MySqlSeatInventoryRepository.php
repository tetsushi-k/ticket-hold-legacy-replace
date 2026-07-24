<?php

declare(strict_types=1);

namespace TicketHold\Infrastructure;

use TicketHold\Domain\BuyerId;
use TicketHold\Domain\PerformanceId;
use TicketHold\Domain\SeatInventory;
use TicketHold\Domain\SeatInventoryRepository;
use TicketHold\Domain\SeatNo;

final class MySqlSeatInventoryRepository implements SeatInventoryRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function find(PerformanceId $performanceId, SeatNo $seatNo): ?SeatInventory
    {
        $stmt = $this->pdo->prepare(
            'SELECT performance_id, seat_no, state, buyer_id, hold_until
             FROM after_seat_inventories
             WHERE performance_id = :performance_id AND seat_no = :seat_no',
        );
        $stmt->execute([
            'performance_id' => $performanceId->value,
            'seat_no' => $seatNo->value,
        ]);
        $row = $stmt->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function findAllByPerformance(PerformanceId $performanceId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT performance_id, seat_no, state, buyer_id, hold_until
             FROM after_seat_inventories
             WHERE performance_id = :performance_id
             ORDER BY seat_no',
        );
        $stmt->execute(['performance_id' => $performanceId->value]);

        $inventories = [];
        while ($row = $stmt->fetch()) {
            $inventories[] = $this->hydrate($row);
        }

        return $inventories;
    }

    public function save(SeatInventory $inventory): void
    {
        $snapshot = $inventory->snapshot();
        $stmt = $this->pdo->prepare(
            'INSERT INTO after_seat_inventories (performance_id, seat_no, state, buyer_id, hold_until)
             VALUES (:performance_id, :seat_no, :state, :buyer_id, :hold_until)
             ON DUPLICATE KEY UPDATE
               state = VALUES(state),
               buyer_id = VALUES(buyer_id),
               hold_until = VALUES(hold_until)',
        );
        $stmt->execute([
            'performance_id' => $snapshot['performanceId'],
            'seat_no' => $snapshot['seatNo'],
            'state' => $snapshot['state'],
            'buyer_id' => $snapshot['buyerId'],
            'hold_until' => $this->normalizeHoldUntil($snapshot['holdUntil']),
        ]);
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): SeatInventory
    {
        $performanceId = new PerformanceId((string) $row['performance_id']);
        $seatNo = new SeatNo((string) $row['seat_no']);

        return match ((string) $row['state']) {
            'available' => SeatInventory::available($performanceId, $seatNo),
            'on_hold' => SeatInventory::onHold(
                $performanceId,
                $seatNo,
                new BuyerId((string) $row['buyer_id']),
                new \DateTimeImmutable((string) $row['hold_until']),
            ),
            'confirmed' => SeatInventory::confirmed(
                $performanceId,
                $seatNo,
                new BuyerId((string) $row['buyer_id']),
            ),
            default => throw new \RuntimeException('Unknown state: ' . $row['state']),
        };
    }

    private function normalizeHoldUntil(?string $holdUntil): ?string
    {
        if ($holdUntil === null) {
            return null;
        }

        return (new \DateTimeImmutable($holdUntil))->format('Y-m-d H:i:s');
    }
}
