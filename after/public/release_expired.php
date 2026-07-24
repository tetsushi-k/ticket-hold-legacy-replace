<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$count = after_app()->releaseExpiredSeats->execute('P1');

header('Location: index.php?msg=released&count=' . $count);
exit;
