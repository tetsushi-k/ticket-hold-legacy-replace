<?php
require_once __DIR__ . '/../lib/db.php';

$eventId = 1;
$res = mysqli_query($conn, "SELECT * FROM seat_rows WHERE event_id = $eventId ORDER BY seat_no");
$seats = [];
while ($row = mysqli_fetch_assoc($res)) {
    $seats[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Ticket Hold Legacy (Before)</title>
  <style>
    body { font-family: sans-serif; margin: 1.5rem; max-width: 720px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 1rem; }
    th, td { border: 1px solid #ccc; padding: 0.4rem 0.6rem; text-align: left; }
    form.inline { display: inline; margin-right: 0.4rem; }
    .note { color: #555; font-size: 0.9rem; }
  </style>
</head>
<body>
  <h1>Ticket Hold Legacy（Before）</h1>
  <p class="note">意図的レガシー。業務ルールは各 PHP に散在。状態語は hold / OK / sold / free が混在。</p>
  <p>公演: Demo Live 2026（event_id=1） / 仮押さえ TTL: <?= (int) $holdMinutes ?> 分</p>

  <table>
    <thead>
      <tr><th>席</th><th>status</th><th>buyer</th><th>hold_until</th><th>操作</th></tr>
    </thead>
    <tbody>
    <?php foreach ($seats as $s): ?>
      <tr>
        <td><?= htmlspecialchars($s['seat_no'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($s['status'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $s['buyer'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $s['hold_until'], ENT_QUOTES, 'UTF-8') ?></td>
        <td>
          <form class="inline" method="post" action="hold.php">
            <input type="hidden" name="event_id" value="1">
            <input type="hidden" name="seat_no" value="<?= htmlspecialchars($s['seat_no'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="buyer" value="buyer-a">
            <button type="submit">仮押さえ(A)</button>
          </form>
          <form class="inline" method="post" action="hold.php">
            <input type="hidden" name="event_id" value="1">
            <input type="hidden" name="seat_no" value="<?= htmlspecialchars($s['seat_no'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="buyer" value="buyer-b">
            <button type="submit">仮押さえ(B)</button>
          </form>
          <form class="inline" method="post" action="confirm.php">
            <input type="hidden" name="event_id" value="1">
            <input type="hidden" name="seat_no" value="<?= htmlspecialchars($s['seat_no'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="buyer" value="buyer-a">
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
  </p>
</body>
</html>
