# 受入例示（Step 3）

> **状態: Step 3 承認済み（2026-07-24 10:55 JST）** — 正本は `acceptance-criteria-questions.md`  
> **原則: 下表の 1 行 = テストの 1 ケース（dataset 行）**

質問ファイルで言う「受入ケース表」＝このファイル内の **H / C / R / Q** の4表（H1〜Q4）。

## 判定・振る舞いの前提（Step 1–2 承認済み）

- 排他単位: **公演 × 座席番号**
- 状態: `Available` / `OnHold(buyerId, holdUntil)` / `Confirmed(buyerId)`（排他）
- Hold TTL: **15 分**。期限切れ OnHold は無効（confirm 不可）。新規 hold 時は差し替え可
- confirm: 有効 OnHold かつ **同一 BuyerId** のみ
- 二重確保: 有効 OnHold または Confirmed があれば hold / confirm とも拒否
- 空き確認 Query: 副作用なし。判定は hold と同じ
- 自動 confirm しない
- タイムゾーン: Asia/Tokyo

## ケース表で使うテストデータ

ケース表の Given / When に出る識別子の一覧。ここに無い値は、そのケースの行にだけ追加で書いてよい。

| 名前 | 値 | 用途 |
|------|-----|------|
| 公演 P1 | `PerformanceId = P1` | 省略時の公演（C / R / Q 含む） |
| 公演 P2 | `PerformanceId = P2` | H5（別公演） |
| 席 A-1 | `SeatNo = A-1` | 省略時の席 |
| 購入者 buyer-a | `BuyerId = buyer-a` | 先に仮押さえする側・本人の confirm |
| 購入者 buyer-b | `BuyerId = buyer-b` | 二重確保・別人の confirm |
| 有効な期限 | `holdUntil` が基準時刻より後 | 表の「有効」な OnHold |
| 期限切れ | `holdUntil` が基準時刻より前 | 表の「期限切れ」な OnHold |
| Hold TTL | 15 分 | 新規 hold 成功時の期限の長さ |

## 受入ケース表

### 仮押さえ（hold）・H1〜H5

| # | caseName | Given | When | Then | なぜ |
|---|----------|-------|------|------|------|
| H1 | 空席を仮押さえできる | P1/A-1 が Available | buyer-a が hold | OnHold(buyer-a, now+15m)。成功 | 基本 |
| H2 | 有効仮押さえがある席への hold を拒否 | P1/A-1 が OnHold(buyer-a, 有効) | buyer-b が hold | 拒否。状態変わらず | 二重確保 |
| H3 | 本確定済み席への hold を拒否 | P1/A-1 が Confirmed(buyer-a) | buyer-b が hold | 拒否 | 二重確保 |
| H4 | 期限切れ仮押さえの席へ hold できる | P1/A-1 が OnHold(buyer-a, 期限切れ) | buyer-b が hold | OnHold(buyer-b, 新期限)。成功 | 空席扱い差し替え |
| H5 | 別公演の同座席番号は独立 | P1/A-1 が OnHold | P2/A-1 へ hold | 成功 | 排他単位 |

### 本確定（confirm）・C1〜C5

| # | caseName | Given | When | Then | なぜ |
|---|----------|-------|------|------|------|
| C1 | 本人が有効仮押さえを本確定できる | OnHold(buyer-a, 有効) | buyer-a が confirm | Confirmed(buyer-a)。成功 | 基本 |
| C2 | 別人の confirm を拒否 | OnHold(buyer-a, 有効) | buyer-b が confirm | 拒否 | 本人のみ |
| C3 | 期限切れ仮押さえの confirm を拒否 | OnHold(buyer-a, 期限切れ) | buyer-a が confirm | 拒否 | 有効でない |
| C4 | 空席の confirm を拒否 | Available | buyer-a が confirm | 拒否 | 仮押さえ前提 |
| C5 | 本確定済みの再 confirm を拒否 | Confirmed(buyer-a) | buyer-a が confirm | 拒否 | 二重 |

### 期限切れ解放（releaseExpired）・R1〜R3

| # | caseName | Given | When | Then | なぜ |
|---|----------|-------|------|------|------|
| R1 | 期限切れ hold を Available に戻す | OnHold(期限切れ) | releaseExpired | Available。成功 | 基本 |
| R2 | 有効 hold は解放しない | OnHold(有効) | releaseExpired | 状態変わらず | 誤解放防止 |
| R3 | Confirmed は解放しない | Confirmed | releaseExpired | 状態変わらず | 本確定は期限対象外 |

### 空き確認（Query・副作用なし）・Q1〜Q4

| # | caseName | Given | When | Then | なぜ |
|---|----------|-------|------|------|------|
| Q1 | 空席は空きあり | Available | 空き確認 | 空きあり。状態不変 | 基本 |
| Q2 | 有効 hold は空きなし | OnHold(有効) | 空き確認 | 空きなし。状態不変 | hold と同判定 |
| Q3 | 期限切れ hold は空きあり | OnHold(期限切れ) | 空き確認 | 空きあり。状態不変 | 空席扱い（書かない） |
| Q4 | Confirmed は空きなし | Confirmed | 空き確認 | 空きなし。状態不変 | |

## シードでデモするシナリオ（make setup 直後）

1. P1 に空席数席 + 必要なら期限切れ hold 用の仕込み
2. **成功**: 空席 hold → 本人 confirm
3. **失敗**: 別人 hold 上書き／別人 confirm／本確定席への hold
4. **期限**: 期限切れ hold の解放、または期限切れ上への新規 hold

## テスト対応

| 表 | テスト置き場 |
|----|-------------|
| H / C / R / Q | `tests/Unit/Domain/SeatInventory*Test.php`（1 ケース = 1 メソッド、名前に H1 等） |
| 同時書き込みの DB 防衛 | Feature（Construction で詳細・未着手） |
