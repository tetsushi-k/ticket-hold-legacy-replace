<?php

declare(strict_types=1);

namespace TicketHold\Infrastructure;

final class PdoFactory
{
    public static function createFromEnv(): \PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $name = getenv('DB_NAME') ?: 'ticket_hold';
        $user = getenv('DB_USER') ?: 'ticket_user';
        $pass = getenv('DB_PASS') ?: 'ticket_password';

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);

        return new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }
}
