<?php

declare(strict_types=1);

namespace TicketHold\Domain;

/** Red 段階の未実装印。Green で削除する。 */
final class NotImplementedException extends \BadMethodCallException
{
    public function __construct(string $method)
    {
        parent::__construct("Red: {$method} is not implemented yet");
    }
}
