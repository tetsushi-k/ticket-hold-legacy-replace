<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/**
 * 集約ルート: 1 公演 × 1 座席。
 * 有効判定（期限切れ OnHold は無効）は Domain 内に閉じる。
 */
final class SeatInventory
{
    public const HOLD_TTL_MINUTES = 15;

    private function __construct(
        private PerformanceId $performanceId,
        private SeatNo $seatNo,
        private SeatReservationState $state,
        private ?BuyerId $buyerId,
        private ?\DateTimeImmutable $holdUntil,
    ) {
    }

    public static function available(PerformanceId $performanceId, SeatNo $seatNo): self
    {
        return new self($performanceId, $seatNo, SeatReservationState::Available, null, null);
    }

    public static function onHold(
        PerformanceId $performanceId,
        SeatNo $seatNo,
        BuyerId $buyerId,
        \DateTimeImmutable $holdUntil,
    ): self {
        return new self($performanceId, $seatNo, SeatReservationState::OnHold, $buyerId, $holdUntil);
    }

    public static function confirmed(
        PerformanceId $performanceId,
        SeatNo $seatNo,
        BuyerId $buyerId,
    ): self {
        return new self($performanceId, $seatNo, SeatReservationState::Confirmed, $buyerId, null);
    }

    public function performanceId(): PerformanceId
    {
        return $this->performanceId;
    }

    public function seatNo(): SeatNo
    {
        return $this->seatNo;
    }

    public function isAvailableState(): bool
    {
        return $this->state === SeatReservationState::Available;
    }

    public function isOnHold(): bool
    {
        return $this->state === SeatReservationState::OnHold;
    }

    public function isConfirmed(): bool
    {
        return $this->state === SeatReservationState::Confirmed;
    }

    public function buyerId(): ?BuyerId
    {
        return $this->buyerId;
    }

    public function holdUntil(): ?\DateTimeImmutable
    {
        return $this->holdUntil;
    }

    /**
     * 状態比較用（「状態変わらず」の Then）。
     *
     * @return array{
     *     performanceId: string,
     *     seatNo: string,
     *     state: string,
     *     buyerId: string|null,
     *     holdUntil: string|null
     * }
     */
    public function snapshot(): array
    {
        return [
            'performanceId' => $this->performanceId->value,
            'seatNo' => $this->seatNo->value,
            'state' => $this->state->value,
            'buyerId' => $this->buyerId?->value,
            'holdUntil' => $this->holdUntil?->format(\DateTimeInterface::ATOM),
        ];
    }

    public function hold(BuyerId $buyerId, \DateTimeImmutable $now): OperationResult
    {
        if ($this->hasEffectiveReservation($now)) {
            return OperationResult::rejected(RejectionReason::DoubleBooking);
        }

        $this->state = SeatReservationState::OnHold;
        $this->buyerId = $buyerId;
        $this->holdUntil = $now->modify('+' . self::HOLD_TTL_MINUTES . ' minutes');

        return OperationResult::ok();
    }

    public function confirm(BuyerId $buyerId, \DateTimeImmutable $now): OperationResult
    {
        if ($this->isConfirmed()) {
            return OperationResult::rejected(RejectionReason::AlreadyConfirmed);
        }
        if ($this->isAvailableState()) {
            return OperationResult::rejected(RejectionReason::NoHold);
        }
        if ($this->isOnHold() && !$this->hasValidHold($now)) {
            return OperationResult::rejected(RejectionReason::HoldExpired);
        }
        if ($this->buyerId === null || !$this->buyerId->equals($buyerId)) {
            return OperationResult::rejected(RejectionReason::NotOwner);
        }

        $this->state = SeatReservationState::Confirmed;
        $this->buyerId = $buyerId;
        $this->holdUntil = null;

        return OperationResult::ok();
    }

    public function releaseExpired(\DateTimeImmutable $now): OperationResult
    {
        if ($this->isConfirmed()) {
            return OperationResult::rejected(RejectionReason::AlreadyConfirmed);
        }
        if (!$this->isOnHold()) {
            return OperationResult::rejected(RejectionReason::NotOnHold);
        }
        if ($this->hasValidHold($now)) {
            return OperationResult::rejected(RejectionReason::HoldNotExpired);
        }

        $this->state = SeatReservationState::Available;
        $this->buyerId = null;
        $this->holdUntil = null;

        return OperationResult::ok();
    }

    /** 空き確認（Query）。副作用なし。判定は hold と同じ「有効確保が無いか」。 */
    public function isAvailable(\DateTimeImmutable $now): bool
    {
        return !$this->hasEffectiveReservation($now);
    }

    /** 有効な OnHold、または Confirmed。 */
    private function hasEffectiveReservation(\DateTimeImmutable $now): bool
    {
        return $this->isConfirmed() || $this->hasValidHold($now);
    }

    /** 期限切れでない OnHold。 */
    private function hasValidHold(\DateTimeImmutable $now): bool
    {
        return $this->isOnHold()
            && $this->holdUntil !== null
            && $this->holdUntil > $now;
    }
}
