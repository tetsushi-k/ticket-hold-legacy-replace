<?php
require_once __DIR__ . '/../lib/db.php';

// intentional debt D4: expiry only cleared when this endpoint is called (no automatic path)
$now = date('Y-m-d H:i:s');
// D3: only status='hold' rows; mixed status vocabulary elsewhere
$sql = "UPDATE seat_rows
        SET status = 'free', buyer = NULL, hold_until = NULL
        WHERE status = 'hold' AND hold_until IS NOT NULL AND hold_until < '$now'";
mysqli_query($conn, $sql);
$n = mysqli_affected_rows($conn);

header('Content-Type: text/plain; charset=UTF-8');
echo "released expired holds: $n\n";
echo "back: /index.php\n";
