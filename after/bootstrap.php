<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use TicketHold\Application\ConfirmSeatUseCase;
use TicketHold\Application\HoldSeatUseCase;
use TicketHold\Application\ListSeatBoardUseCase;
use TicketHold\Application\ReleaseExpiredSeatsUseCase;
use TicketHold\Domain\OperationResult;
use TicketHold\Infrastructure\MySqlSeatInventoryRepository;
use TicketHold\Infrastructure\PdoFactory;
use TicketHold\Infrastructure\SystemClock;

final readonly class AfterApp
{
    public HoldSeatUseCase $holdSeat;
    public ConfirmSeatUseCase $confirmSeat;
    public ReleaseExpiredSeatsUseCase $releaseExpiredSeats;
    public ListSeatBoardUseCase $listSeatBoard;

    public function __construct()
    {
        $repository = new MySqlSeatInventoryRepository(PdoFactory::createFromEnv());
        $clock = new SystemClock(new DateTimeZone('Asia/Tokyo'));
        $this->holdSeat = new HoldSeatUseCase($repository, $clock);
        $this->confirmSeat = new ConfirmSeatUseCase($repository, $clock);
        $this->releaseExpiredSeats = new ReleaseExpiredSeatsUseCase($repository, $clock);
        $this->listSeatBoard = new ListSeatBoardUseCase($repository, $clock);
    }
}

function after_app(): AfterApp
{
    static $app = null;
    if ($app === null) {
        $app = new AfterApp();
    }

    return $app;
}

function after_redirect(OperationResult $result, string $okMessage): never
{
    $msg = $result->isOk()
        ? $okMessage
        : ($result->rejectionReason()?->value ?? 'rejected');
    header('Location: index.php?msg=' . rawurlencode($msg));
    exit;
}
