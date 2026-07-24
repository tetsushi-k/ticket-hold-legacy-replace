# Intent（題材の意図と境界）

> **状態: Step 1 承認済み（2026-07-23 22:42 JST）** — 正本は `intent-approval-questions.md`

## 何を作るか

興行チケットの座席について、**仮押さえ → 本確定／期限切れ解放**、および **同一席の二重確保拒否** を扱うコアを、意図的レガシー（Before）から仕様復元し、DG-AIDLC でドメイン抽出した After まで同一リポで比較できるようにする。

## なぜ作るか

- [`salon-booking-ddd`](../../salon-booking-ddd) が Greenfield で型の成立を証明するのに対し、本作は **別ドメインの Brownfield** で型の横展開を証明する
- Zenn「DG-AIDLC 手法確立」記事の第2作前提。主役は `aidlc-docs/`
- Reverse Engineering を省略しない（意図的負債を先に埋め、そこから復元する）

## スコープ（v1・承認済み）

1. 仮押さえ作成
2. 本確定
3. 期限切れ解放
4. 二重確保拒否（同一席に有効な仮押さえ／確定が二重にならない）

### 核心判断（Answer 反映）

| 項目 | 確定 |
|------|------|
| 排他単位 | **公演 × 座席番号**（Q1=A） |
| 仮押さえ TTL | **15 分**（Q2=B） |
| 期限切れ後 | **すぐ空席扱い**。期限切れ仮押さえからの本確定は不可（Q3=A） |
| 本確定条件 | 有効期限内の仮押さえがあり、**購入者 ID 一致**のときだけ（Q4=A） |
| 二重確保 | 有効な仮押さえ **または** 本確定が既にあれば、新規仮押さえも本確定も拒否（Q5=A） |
| 期限切れ行の残存 | 期限超過なら **空席扱い**で新規仮押さえ可（必要なら旧行を解放）（Q6=A） |
| 同時書き込み | アプリ判定 + **DB 側の二重防止**（Q7=A） |
| 空き確認 | **副作用なし**。判定は仮押さえ時と同じ（Q8=A） |
| 自動本確定 | **しない**（安全側）（Q9=A） |
| 購入者 | **購入者 ID のみ**（会員マスタなし）（Q10=A） |
| After 構成 | **素の PHP 8.3** + Domain / Application / Infrastructure。境界は Deptrac 等（Q11=A） |

## スコープ外

| 除外 | 理由（v1） |
|------|------------|
| 決済の本実装 | コアと無関係／後置 |
| 会員登録・OAuth | コアと無関係 |
| 検索・おすすめ・在庫 UI の過剰 | デモを小さく保つ |
| 複数会場の複雑な在庫 | 排他単位を単純に保つ |
| 通知メール・管理画面の過剰機能 | スコープ外 |
| Laravel | Q11 で素の PHP を選択 |

## 成功の定義（Done）

| # | 条件 | 状態（2026-07-24・Domain 区切り） |
|---|------|-----------------------------------|
| 1 | Before（`legacy/`）と After が同一リポで比較できる | 一部（Domain のみ。Infra / Presentation 未） |
| 2 | スコープ4点が受入テストで証明できる（受入例示 1:1） | **済**（Domain Unit 17） |
| 3 | `decision-log` に「AI 草案を人間が却下／修正した核心」が最低1件 | **済**（Step 1 Laravel 却下） |
| 4 | `make setup` で主要動作が再現できる | 一部（Before のみ） |
| 5 | README から起動・動作確認・設計の要点が読める | **済** |
| 6 | Domain 境界違反を Deptrac（または同等）で証明できる | **済** |

詳細は [`decision-log.md`](../decision-log.md) Step 7。

## 技術スタック（承認済み）

| 層 | Before | After |
|----|--------|-------|
| 言語 | PHP 8.3（手続き型・意図的負債） | PHP 8.3 |
| 構成 | 画面ごと PHP + グローバル DB | Domain / Application / Infrastructure |
| 境界証明 | なし | Deptrac + PHPStan（`make check`・Domain 済） |
| DB | MySQL 8（compose） | 同左 |
| DX | Makefile + docker compose | 同左に統合 |

## 関連ドキュメント

- 意図的負債計画: `intentional-debt-plan.md`
- Reverse Engineering: `reverse-engineering/`
- Greenfield 対照: `salon-booking-ddd`
