<?php

declare(strict_types=1);

namespace TicketHold\Tests\Unit\Domain;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use TicketHold\Domain\BuyerId;
use TicketHold\Domain\PerformanceId;
use TicketHold\Domain\RejectionReason;
use TicketHold\Domain\SeatInventory;
use TicketHold\Domain\SeatNo;

/**
 * 受入ケース H1〜H5（acceptance-criteria.md）
 */
final class SeatInventoryHoldTest extends TestCase
{
    private DateTimeZone $tz;
    private DateTimeImmutable $now;
    private PerformanceId $p1;
    private PerformanceId $p2;
    private SeatNo $a1;
    private BuyerId $buyerA;
    private BuyerId $buyerB;

    protected function setUp(): void
    {
        $this->tz = new DateTimeZone('Asia/Tokyo');
        $this->now = new DateTimeImmutable('2026-07-24 12:00:00', $this->tz);
        $this->p1 = new PerformanceId('P1');
        $this->p2 = new PerformanceId('P2');
        $this->a1 = new SeatNo('A-1');
        $this->buyerA = new BuyerId('buyer-a');
        $this->buyerB = new BuyerId('buyer-b');
    }

    /** H1 空席を仮押さえできる */
    public function test_H1_empty_seat_can_be_held(): void
    {
        $seat = SeatInventory::available($this->p1, $this->a1);

        $result = $seat->hold($this->buyerA, $this->now);

        $this->assertTrue($result->isOk());
        $this->assertTrue($seat->isOnHold());
        $this->assertTrue($seat->buyerId()?->equals($this->buyerA));
        $expectedUntil = $this->now->modify('+' . SeatInventory::HOLD_TTL_MINUTES . ' minutes');
        $this->assertEquals($expectedUntil, $seat->holdUntil());
    }

    /** H2 有効仮押さえがある席への hold を拒否 */
    public function test_H2_reject_hold_when_valid_hold_exists(): void
    {
        $validUntil = $this->now->modify('+10 minutes');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $validUntil);
        $before = $seat->snapshot();

        $result = $seat->hold($this->buyerB, $this->now);

        $this->assertFalse($result->isOk());
        $this->assertSame(RejectionReason::DoubleBooking, $result->rejectionReason());
        $this->assertSame($before, $seat->snapshot());
    }

    /** H3 本確定済み席への hold を拒否 */
    public function test_H3_reject_hold_when_confirmed(): void
    {
        $seat = SeatInventory::confirmed($this->p1, $this->a1, $this->buyerA);
        $before = $seat->snapshot();

        $result = $seat->hold($this->buyerB, $this->now);

        $this->assertFalse($result->isOk());
        $this->assertSame(RejectionReason::DoubleBooking, $result->rejectionReason());
        $this->assertSame($before, $seat->snapshot());
    }

    /** H4 期限切れ仮押さえの席へ hold できる（差し替え） */
    public function test_H4_hold_replaces_expired_hold(): void
    {
        $expiredUntil = $this->now->modify('-1 minute');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $expiredUntil);

        $result = $seat->hold($this->buyerB, $this->now);

        $this->assertTrue($result->isOk());
        $this->assertTrue($seat->isOnHold());
        $this->assertTrue($seat->buyerId()?->equals($this->buyerB));
        $expectedUntil = $this->now->modify('+' . SeatInventory::HOLD_TTL_MINUTES . ' minutes');
        $this->assertEquals($expectedUntil, $seat->holdUntil());
    }

    /** H5 別公演の同座席番号は独立 */
    public function test_H5_same_seat_no_on_other_performance_is_independent(): void
    {
        $validUntil = $this->now->modify('+10 minutes');
        $p1Seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $validUntil);
        $p2Seat = SeatInventory::available($this->p2, $this->a1);

        $result = $p2Seat->hold($this->buyerB, $this->now);

        $this->assertTrue($result->isOk());
        $this->assertTrue($p2Seat->isOnHold());
        $this->assertTrue($p1Seat->isOnHold());
        $this->assertTrue($p1Seat->buyerId()?->equals($this->buyerA));
    }
}
