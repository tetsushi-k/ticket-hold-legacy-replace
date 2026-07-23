<?php
// intentional debt: magic env defaults, no validation
$dbHost = getenv('DB_HOST') ?: 'db';
$dbName = getenv('DB_NAME') ?: 'ticket_hold';
$dbUser = getenv('DB_USER') ?: 'ticket_user';
$dbPass = getenv('DB_PASS') ?: 'ticket_password';
$holdMinutes = (int) (getenv('HOLD_MINUTES') ?: 15);
