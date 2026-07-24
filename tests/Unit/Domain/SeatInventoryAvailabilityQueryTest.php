<?php

declare(strict_types=1);

namespace TicketHold\Tests\Unit\Domain;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use TicketHold\Domain\BuyerId;
use TicketHold\Domain\PerformanceId;
use TicketHold\Domain\SeatInventory;
use TicketHold\Domain\SeatNo;

/**
 * 受入ケース Q1〜Q4（acceptance-criteria.md）
 * Domain の isAvailable（読取・副作用なし）。Application Query は Green 以降で薄いラッパ可。
 */
final class SeatInventoryAvailabilityQueryTest extends TestCase
{
    private DateTimeImmutable $now;
    private PerformanceId $p1;
    private SeatNo $a1;
    private BuyerId $buyerA;

    protected function setUp(): void
    {
        $tz = new DateTimeZone('Asia/Tokyo');
        $this->now = new DateTimeImmutable('2026-07-24 12:00:00', $tz);
        $this->p1 = new PerformanceId('P1');
        $this->a1 = new SeatNo('A-1');
        $this->buyerA = new BuyerId('buyer-a');
    }

    /** Q1 空席は空きあり */
    public function test_Q1_available_seat_is_available(): void
    {
        $seat = SeatInventory::available($this->p1, $this->a1);
        $before = $seat->snapshot();

        $this->assertTrue($seat->isAvailable($this->now));
        $this->assertSame($before, $seat->snapshot());
    }

    /** Q2 有効 hold は空きなし */
    public function test_Q2_valid_hold_is_not_available(): void
    {
        $validUntil = $this->now->modify('+10 minutes');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $validUntil);
        $before = $seat->snapshot();

        $this->assertFalse($seat->isAvailable($this->now));
        $this->assertSame($before, $seat->snapshot());
    }

    /** Q3 期限切れ hold は空きあり（書かない） */
    public function test_Q3_expired_hold_is_available_without_write(): void
    {
        $expiredUntil = $this->now->modify('-1 minute');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $expiredUntil);
        $before = $seat->snapshot();

        $this->assertTrue($seat->isAvailable($this->now));
        $this->assertSame($before, $seat->snapshot());
    }

    /** Q4 Confirmed は空きなし */
    public function test_Q4_confirmed_is_not_available(): void
    {
        $seat = SeatInventory::confirmed($this->p1, $this->a1, $this->buyerA);
        $before = $seat->snapshot();

        $this->assertFalse($seat->isAvailable($this->now));
        $this->assertSame($before, $seat->snapshot());
    }
}
