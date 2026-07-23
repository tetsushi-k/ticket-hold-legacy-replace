<?php
require_once __DIR__ . '/../lib/db.php';

// intentional debt D2/D4: confirm path barely checks expiry; buyer match is weak
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

// D3: accepts both hold and already OK-ish states inconsistently
if ($row['status'] !== 'hold') {
    header('Location: index.php?err=not_on_hold');
    exit;
}

// D4: expired hold can still be confirmed if release_expired was never hit
$upd = "UPDATE seat_rows SET status = 'OK', hold_until = NULL
        WHERE event_id = $eventId AND seat_no = '$seatNo'";
mysqli_query($conn, $upd);

header('Location: index.php?ok=confirmed');
exit;
