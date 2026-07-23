# アンチパターン一覧（意図的負債の観測）

計画は `../intentional-debt-plan.md`。本ファイルは `legacy/` 実装からの観測結果。

| ID | 観測箇所 | 内容 | Intent での解消方針 |
|----|----------|------|---------------------|
| D1 | `lib/db.php`, `hold.php` 等 | グローバル `$conn`、条件の SQL 文字列連結 | リポジトリ + プリペアド |
| D2 | `hold.php` / `confirm.php` / `release_expired.php` | 業務 if が画面ごとに散在・複製 | Domain 不変条件 + UseCase |
| D3 | `seat_rows.status` | `free` / `hold` / `OK`（＋コード上 `sold`）が混在 | 明示状態（Available / Hold / Confirmed） |
| D4 | `confirm.php`, `release_expired.php` | 期限は解放エンドポイント任せ。confirm は期限を見ない | 「有効」判定を Domain に集約。期限切れは空席扱い（Q3/Q6） |
| D5 | `hold.php` | 本確定済みだけ拒否。有効 `hold` は上書き可 | 有効確保の拒否（Q5）+ DB 防衛（Q7） |
| D6 | （専用 API なし） | 照会と更新の境界が UI に埋没 | Query（副作用なし）と Command を分離（Q8） |

## デモで見える穴

1. A が仮押さえした席を、B の「仮押さえ(B)」で上書きできる（D5）
2. 期限切れでも `release_expired` 前なら「本確定」できうる（D4）
3. 本確定ボタンは常に buyer-a 固定で、hold の buyer と不一致でも通る（D2/D4）
