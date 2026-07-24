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
 * 受入ケース C1〜C5（acceptance-criteria.md）
 */
final class SeatInventoryConfirmTest extends TestCase
{
    private DateTimeImmutable $now;
    private PerformanceId $p1;
    private SeatNo $a1;
    private BuyerId $buyerA;
    private BuyerId $buyerB;

    protected function setUp(): void
    {
        $tz = new DateTimeZone('Asia/Tokyo');
        $this->now = new DateTimeImmutable('2026-07-24 12:00:00', $tz);
        $this->p1 = new PerformanceId('P1');
        $this->a1 = new SeatNo('A-1');
        $this->buyerA = new BuyerId('buyer-a');
        $this->buyerB = new BuyerId('buyer-b');
    }

    /** C1 本人が有効仮押さえを本確定できる */
    public function test_C1_owner_can_confirm_valid_hold(): void
    {
        $validUntil = $this->now->modify('+10 minutes');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $validUntil);

        $result = $seat->confirm($this->buyerA, $this->now);

        $this->assertTrue($result->isOk());
        $this->assertTrue($seat->isConfirmed());
        $this->assertTrue($seat->buyerId()?->equals($this->buyerA));
        $this->assertNull($seat->holdUntil());
    }

    /** C2 別人の confirm を拒否 */
    public function test_C2_reject_confirm_by_other_buyer(): void
    {
        $validUntil = $this->now->modify('+10 minutes');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $validUntil);
        $before = $seat->snapshot();

        $result = $seat->confirm($this->buyerB, $this->now);

        $this->assertFalse($result->isOk());
        $this->assertSame($before, $seat->snapshot());
    }

    /** C3 期限切れ仮押さえの confirm を拒否 */
    public function test_C3_reject_confirm_when_hold_expired(): void
    {
        $expiredUntil = $this->now->modify('-1 minute');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $expiredUntil);
        $before = $seat->snapshot();

        $result = $seat->confirm($this->buyerA, $this->now);

        $this->assertFalse($result->isOk());
        $this->assertSame($before, $seat->snapshot());
    }

    /** C4 空席の confirm を拒否 */
    public function test_C4_reject_confirm_when_available(): void
    {
        $seat = SeatInventory::available($this->p1, $this->a1);
        $before = $seat->snapshot();

        $result = $seat->confirm($this->buyerA, $this->now);

        $this->assertFalse($result->isOk());
        $this->assertSame($before, $seat->snapshot());
    }

    /** C5 本確定済みの再 confirm を拒否 */
    public function test_C5_reject_confirm_when_already_confirmed(): void
    {
        $seat = SeatInventory::confirmed($this->p1, $this->a1, $this->buyerA);
        $before = $seat->snapshot();

        $result = $seat->confirm($this->buyerA, $this->now);

        $this->assertFalse($result->isOk());
        $this->assertSame($before, $seat->snapshot());
    }
}
