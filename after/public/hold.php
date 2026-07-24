<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$performanceId = $_POST['performance_id'] ?? '';
$seatNo = $_POST['seat_no'] ?? '';
$buyerId = $_POST['buyer_id'] ?? '';

if ($performanceId === '' || $seatNo === '' || $buyerId === '') {
    http_response_code(400);
    exit('bad request');
}

$result = after_app()->holdSeat->execute($performanceId, $seatNo, $buyerId);

after_redirect($result, 'held');
