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
 * 受入ケース R1〜R3（acceptance-criteria.md）
 */
final class SeatInventoryReleaseExpiredTest extends TestCase
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

    /** R1 期限切れ hold を Available に戻す */
    public function test_R1_release_expired_hold_to_available(): void
    {
        $expiredUntil = $this->now->modify('-1 minute');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $expiredUntil);

        $result = $seat->releaseExpired($this->now);

        $this->assertTrue($result->isOk());
        $this->assertTrue($seat->isAvailableState());
        $this->assertNull($seat->buyerId());
        $this->assertNull($seat->holdUntil());
    }

    /** R2 有効 hold は解放しない */
    public function test_R2_do_not_release_valid_hold(): void
    {
        $validUntil = $this->now->modify('+10 minutes');
        $seat = SeatInventory::onHold($this->p1, $this->a1, $this->buyerA, $validUntil);
        $before = $seat->snapshot();

        $result = $seat->releaseExpired($this->now);

        $this->assertFalse($result->isOk());
        $this->assertSame(RejectionReason::HoldNotExpired, $result->rejectionReason());
        $this->assertSame($before, $seat->snapshot());
    }

    /** R3 Confirmed は解放しない */
    public function test_R3_do_not_release_confirmed(): void
    {
        $seat = SeatInventory::confirmed($this->p1, $this->a1, $this->buyerA);
        $before = $seat->snapshot();

        $result = $seat->releaseExpired($this->now);

        $this->assertFalse($result->isOk());
        $this->assertSame(RejectionReason::AlreadyConfirmed, $result->rejectionReason());
        $this->assertSame($before, $seat->snapshot());
    }
}
