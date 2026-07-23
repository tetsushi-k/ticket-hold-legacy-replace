# 集約・ドメイン境界

> **状態: Step 2 承認済み（2026-07-23 22:51 JST）** — 正本は `domain-modeling-questions.md`

## 集約ルート

- **SeatInventory**: **1 公演 × 1 座席番号**（Q1=A）
  - 状態は排他的に 1 つ（Q2=A）: `Available` / `OnHold(buyerId, holdUntil)` / `Confirmed(buyerId)`
  - 操作: `hold` / `confirm` / `releaseExpired`（バッチ相当）。期限切れを踏まえた `hold` は集約内で差し替え（Q4=A）

## 不変条件

1. 同一公演・同一席に、**有効な OnHold と Confirmed は高々 1**（二重の有効確保なし）
2. Hold の TTL は **15 分**（Intent）
3. `confirm` は **有効 OnHold かつ同一 BuyerId** のときだけ
4. 期限切れ OnHold は有効でない → `confirm` 不可。新規 `hold` 時は空席扱いして差し替え可（Q4=A）
5. 自動 `confirm` しない（Intent）
6. 「有効な仮押さえか」の判定は **Domain 内**（Q3=A）。UseCase / SQL に散らさない

## 照会とコマンド

- **Query（空き確認）** と **Command（仮押さえ等）** を分ける（Q6=A）
- Query は副作用なし。判定は Command と同じ「有効確保が無いか」

## ドメインサービス

- 当面なし（席集約内で閉じる）

## 永続化ポート

- interface: Domain（書込）／必要なら Application（読取ポート）
- 実装: Infrastructure（MySQL）
- DB 防衛: **有効な確保だけが一意**になる仕組み（Q5=A）。詳細は Construction

## 境界検証

- Deptrac: Domain → FW / mysqli / HTTP 依存ゼロ
- PHPStan（予定）

## 人間が確定した判断（Step 2）

| Q | 確定 |
|---|------|
| 1 | 集約 = 公演×1 席 |
| 2 | 状態は Available / OnHold / Confirmed の排他 |
| 3 | 有効判定は Domain |
| 4 | 期限切れ hold 上への新規 hold は差し替え |
| 5 | 有効確保の一意を DB でも守る |
| 6 | Query / Command 分離 |
