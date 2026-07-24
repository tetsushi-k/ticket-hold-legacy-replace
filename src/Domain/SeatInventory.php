<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/**
 * 集約ルート: 1 公演 × 1 座席。
 *
 * ファクトリは Given 用の状態復元のみ。hold / confirm / releaseExpired / isAvailable
 * の業務ルールは Green で実装する（いまは Red）。
 */
final class SeatInventory
{
    public const HOLD_TTL_MINUTES = 15;

    private function __construct(
        private PerformanceId $performanceId,
        private SeatNo $seatNo,
        private string $state,
        private ?BuyerId $buyerId,
        private ?\DateTimeImmutable $holdUntil,
    ) {
    }

    public static function available(PerformanceId $performanceId, SeatNo $seatNo): self
    {
        return new self($performanceId, $seatNo, 'available', null, null);
    }

    public static function onHold(
        PerformanceId $performanceId,
        SeatNo $seatNo,
        BuyerId $buyerId,
        \DateTimeImmutable $holdUntil,
    ): self {
        return new self($performanceId, $seatNo, 'on_hold', $buyerId, $holdUntil);
    }

    public static function confirmed(
        PerformanceId $performanceId,
        SeatNo $seatNo,
        BuyerId $buyerId,
    ): self {
        return new self($performanceId, $seatNo, 'confirmed', $buyerId, null);
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
        return $this->state === 'available';
    }

    public function isOnHold(): bool
    {
        return $this->state === 'on_hold';
    }

    public function isConfirmed(): bool
    {
        return $this->state === 'confirmed';
    }

    public function buyerId(): ?BuyerId
    {
        return $this->buyerId;
    }

    public function holdUntil(): ?\DateTimeImmutable
    {
        return $this->holdUntil;
    }

    /** 状態比較用（「状態変わらず」の Then）。 */
    public function snapshot(): array
    {
        return [
            'performanceId' => $this->performanceId->value,
            'seatNo' => $this->seatNo->value,
            'state' => $this->state,
            'buyerId' => $this->buyerId?->value,
            'holdUntil' => $this->holdUntil?->format(\DateTimeInterface::ATOM),
        ];
    }

    public function hold(BuyerId $buyerId, \DateTimeImmutable $now): OperationResult
    {
        throw new NotImplementedException('SeatInventory::hold');
    }

    public function confirm(BuyerId $buyerId, \DateTimeImmutable $now): OperationResult
    {
        throw new NotImplementedException('SeatInventory::confirm');
    }

    public function releaseExpired(\DateTimeImmutable $now): OperationResult
    {
        throw new NotImplementedException('SeatInventory::releaseExpired');
    }

    /** 空き確認（Query）。副作用なし。判定は hold と同じ「有効確保が無いか」。 */
    public function isAvailable(\DateTimeImmutable $now): bool
    {
        throw new NotImplementedException('SeatInventory::isAvailable');
    }
}
