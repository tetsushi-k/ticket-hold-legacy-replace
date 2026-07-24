<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$performanceId = 'P1';
$app = after_app();
$seats = $app->listSeatBoard->execute($performanceId);
$message = match ($_GET['msg'] ?? '') {
    'held' => '仮押さえしました。',
    'confirmed' => '本確定しました。',
    'released' => '期限切れ仮押さえを解放しました（' . (int) ($_GET['count'] ?? 0) . ' 席）。',
    'double_booking' => '拒否: 有効な仮押さえまたは本確定があるため、仮押さえできません。',
    'not_owner' => '拒否: 本人の仮押さえのみ本確定できます。',
    'hold_expired' => '拒否: 仮押さえの期限が切れています。再度仮押さえしてください。',
    'no_hold' => '拒否: 仮押さえがない席は本確定できません。',
    'already_confirmed' => '拒否: すでに本確定済みです。',
    'hold_not_expired' => '拒否: 有効な仮押さえは期限切れ解放の対象外です。',
    'not_on_hold' => '拒否: 仮押さえ中の席だけ解放できます。',
    'seat_not_found' => '拒否: 席が見つかりません。',
    default => '',
};
$isError = str_starts_with($message, '拒否:');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Ticket Hold After</title>
  <style>
    body { font-family: sans-serif; margin: 1.5rem; max-width: 820px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 1rem; }
    th, td { border: 1px solid #ccc; padding: 0.4rem 0.6rem; text-align: left; }
    form.inline { display: inline; margin-right: 0.4rem; }
    .note { color: #555; font-size: 0.9rem; }
    .flash { padding: 0.5rem 0.75rem; margin-bottom: 1rem; background: #f0f7ff; border: 1px solid #bcd; }
    .flash.err { background: #fff0f0; border-color: #ebb; }
  </style>
</head>
<body>
  <h1>Ticket Hold After</h1>
  <p class="note">Domain / Application / Infrastructure。状態は available / on_hold / confirmed。空き確認は書き込まない。</p>
  <p>公演: Demo Live 2026（<?= htmlspecialchars($performanceId, ENT_QUOTES, 'UTF-8') ?>） / TTL: 15 分</p>

  <?php if ($message !== ''): ?>
    <p class="flash<?= $isError ? ' err' : '' ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>席</th>
        <th>state</th>
        <th>buyer</th>
        <th>hold_until</th>
        <th>空き確認</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($seats as $seat): ?>
      <tr>
        <td><?= htmlspecialchars($seat->seatNo, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($seat->state, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $seat->buyerId, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $seat->holdUntil, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= $seat->isAvailable ? '空きあり' : '空きなし' ?></td>
        <td>
          <form class="inline" method="post" action="hold.php">
            <input type="hidden" name="performance_id" value="<?= htmlspecialchars($performanceId, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="seat_no" value="<?= htmlspecialchars($seat->seatNo, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="buyer_id" value="buyer-a">
            <button type="submit">仮押さえ(A)</button>
          </form>
          <form class="inline" method="post" action="hold.php">
            <input type="hidden" name="performance_id" value="<?= htmlspecialchars($performanceId, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="seat_no" value="<?= htmlspecialchars($seat->seatNo, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="buyer_id" value="buyer-b">
            <button type="submit">仮押さえ(B)</button>
          </form>
          <form class="inline" method="post" action="confirm.php">
            <input type="hidden" name="performance_id" value="<?= htmlspecialchars($performanceId, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="seat_no" value="<?= htmlspecialchars($seat->seatNo, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="buyer_id" value="buyer-a">
            <button type="submit">本確定(A)</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <p>
    <a href="release_expired.php">期限切れ解放を実行</a>
    ・
    <a href="index.php">再読込</a>
    ・
    <a href="http://localhost:8080/" target="_blank" rel="noopener">Before（legacy）</a>
  </p>
</body>
</html>
