# 仕様復元（Reverse Engineering）

> 対象: `legacy/`（意図的 Before）。復元日: 2026-07-23。  
> Intent（After の正しい仕様）は別途承認済み。本ファイルは **コードから読める事実** と **Intent との差分** を残す。

## 業務概要

興行の座席について、仮押さえ・本確定・期限切れ解放を扱う最小 UI。決済・会員・検索は無い。

## ビジネス・トランザクション（コード上）

| 処理 | 入口 | 観測された振る舞い |
|------|------|-------------------|
| 仮押さえ | `POST hold.php` | `status='hold'`、`buyer`、`hold_until=now+HOLD_MINUTES` を UPDATE |
| 本確定 | `POST confirm.php` | `status='hold'` なら `status='OK'` に UPDATE（購入者・期限をほぼ見ない） |
| 期限切れ解放 | `GET release_expired.php` | `hold` かつ `hold_until < now` を `free` に UPDATE |
| 一覧 | `GET index.php` | `seat_rows` を表示。操作ボタン付き |

## データ（観測）

- `events`: 公演マスタ（シードで id=1）
- `seat_rows`: `event_id` + `seat_no` 一意。`status` / `buyer` / `hold_until`
- 状態語の実測値: `free` / `hold` / `OK`（コメント上は `sold` も想定）

## 設定

- `HOLD_MINUTES` 環境変数（未設定時 15）。画面にも表示

## Intent（承認済み）との差分（重要）

| 観点 | legacy（事実） | Intent（After で守る） |
|------|----------------|------------------------|
| 二重仮押さえ | `sold`/`OK` 以外は上書き可（有効 hold を拒否しない） | 有効 hold / 本確定があれば拒否 |
| 本確定 | 購入者一致・期限を見ない | 本人 + 有効期限内のみ |
| 期限切れ | 手動 `release_expired` まで塞がり得る／期限切れでも本確定できうる | 期限超過は空席扱い。本確定不可 |
| 状態語 | `OK` 等のゆれ | Confirmed / Hold / Available に揃える |
| 空き照会 | 専用 Query なし（画面が行を表示するだけ） | 副作用なし Query（判定は仮押さえと同じ） |
| 同時書き込み | チェック→UPDATE のみ | アプリ + DB 防衛 |

## 次工程

差分をドメイン不変条件・受入例示に落とし、After で証明する（Step 2–3 → Red/Green）。
