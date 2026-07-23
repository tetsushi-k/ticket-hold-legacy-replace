<?php
require_once __DIR__ . '/../lib/db.php';

// intentional debt D1/D2/D5: SQL concat, rules in page, sold-only check (active hold ignored)
$eventId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
$seatNo = isset($_POST['seat_no']) ? mysqli_real_escape_string($conn, $_POST['seat_no']) : '';
$buyer = isset($_POST['buyer']) ? mysqli_real_escape_string($conn, $_POST['buyer']) : '';

if ($eventId <= 0 || $seatNo === '' || $buyer === '') {
    die('bad request');
}

$sql = "SELECT * FROM seat_rows WHERE event_id = $eventId AND seat_no = '$seatNo' LIMIT 1";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
if (!$row) {
    die('seat not found');
}

// D5: only reject when already "sold" / "OK" — active "hold" is NOT treated as blocking
if ($row['status'] === 'sold' || $row['status'] === 'OK') {
    header('Location: index.php?err=already_sold');
    exit;
}

// D3: status word "hold"; D4: expiry not consulted here when overwriting another hold
$until = date('Y-m-d H:i:s', time() + ($holdMinutes * 60));
$upd = "UPDATE seat_rows SET status = 'hold', buyer = '$buyer', hold_until = '$until'
        WHERE event_id = $eventId AND seat_no = '$seatNo'";
mysqli_query($conn, $upd);

header('Location: index.php?ok=held');
exit;
