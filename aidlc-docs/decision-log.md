# 判断ログ（decision-log）

各ステップ末に「AI に任せた / 自分が判断・修正した / 重かった点」を 1〜3 行残す。  
**核心の選択肢変更は `*-questions.md` の Answer が正本。** 本ファイルは要約。

## リポ立ち上げ（2026-07-23）

- **AI に任せた**: キット配置、Intent 質問案、意図的負債計画、レガシー最小骨格
- **自分が判断**: 主言語 PHP、題材（仮押さえコア）、Brownfield として Before 先行
- **重かった / 迷った**: —

## Step 1: Intent & 境界（承認 2026-07-23 22:42 JST）

- **AI に任せた**: Q1–Q12 の選択肢文面・Intent 草案・TTL デフォルト 15 分の Before 実装
- **自分が判断**: 全 Answer（排他=公演×席、TTL=15、本人のみ本確定、二重は仮押さえも含む拒否、期限切れは空席扱い、DB 防衛あり、空きは副作用なし、自動本確定なし、購入者は ID、After=素の PHP+レイヤ）
- **重かった / 迷った**: After を Laravel にするか素の PHP にするか。横展開の見え方を優先し **素の PHP 8.3 + Deptrac**（Q11=A）を選んだ（AI 草案の B 候補を却下）

## Step 2: ドメインモデリング（承認 2026-07-23 22:51 JST）

- **AI に任せた**: 集約案（SeatInventory）・質問文面・aggregate 草案
- **自分が判断**: Q1–Q7 全 Answer（集約=公演×席、状態排他、有効判定は Domain、期限切れは差し替え、DB 一意、Query/Command 分離）
- **重かった / 迷った**: Q3（有効判定の置き場）。DDD セオリーに沿い Domain（A）を選んだ

## Step 3: 受入例示（承認 2026-07-24 10:55 JST）

- **AI に任せた**: 受入ケース表（H/C/R/Q）草案・質問文面・シードデモ案
- **自分が判断**: Q1–Q3 全 Answer（表どおりでテスト正解、デモ用シードは5場面で足りる、Step 3 閉じる）
- **重かった / 迷った**: —

## Step 4–6: Red → Green → Refactor

実装フェーズでは **ドメインの新規判断は増やさず**、Step 1–3 の正本（Intent / 集約 / 受入）に閉じ込める方針を維持した（Never Vibe Code）。

### Step 4: Red

- **AI に任せた**: composer / PHPUnit 骨格、H1–H5 / C1–C5 / R1–R3 / Q1–Q4 の Domain Unit（17 ケース）、`make test`（Docker `php` サービス）
- **自分が判断**: Step 3 承認後に Red 着手。受入表 1 行 = 1 テストメソッドの対応を維持
- **重かった / 迷った**: —
- **次**: Green

### Step 5: Green

- **AI に任せた**: `SeatInventory` の hold / confirm / releaseExpired / isAvailable 最小実装
- **自分が判断**: 拒否は例外ではなく `OperationResult::rejected()`（受入表の「拒否」表現に合わせる）。有効判定は private メソッドに集約
- **重かった / 迷った**: —
- **次**: Step 6 Refactor

### Step 6: Refactor & 境界検証

- **AI に任せた**: `SeatReservationState` enum、`SeatInventoryRepository` port、Deptrac / PHPStan / `make check`
- **自分が判断**: レイヤ ruleset は Intent の依存方向（Domain 逆流禁止）に合わせた。`legacy/` は PHPStan 対象外のまま
- **重かった / 迷った**: —
- **次**: Step 7 ナラティブ

## Step 7: ナラティブ（2026-07-24 12:03 JST）

- **AI に任せた**: decision-log の Step 4–6 分割、README / aidlc-docs / intent の完了体裁、`make check` 実行確認
- **自分が判断**: **Domain 抽出までを Construction の Step 7 区切り**とする。Application / Infrastructure / DB 防衛 Feature は Intent Done の残りとして明示し、偽って「全面リプレイス完了」とは書かない
- **重かった / 迷った**: Brownfield では RE が Step 4 より前（Inception）にある点を、読み順で迷わないよう aidlc-docs README に維持

### Intent Done との対応（Domain 区切り時点）

| # | 条件 | 状態 |
|---|------|------|
| 1 | Before / After 同一リポで比較 | **一部** — `legacy/` + `src/Domain/`。Presentation / Infra は未 |
| 2 | スコープ 4 点を受入テストで証明 | **済** — Domain Unit 17 ケース |
| 3 | decision-log に人間の核心判断 | **済** — Step 1 で Laravel 却下ほか |
| 4 | `make setup` で主要動作再現 | **一部** — Before のみ。After シードは未 |
| 5 | README から起動・設計が読める | **済** |
| 6 | Deptrac で Domain 境界証明 | **済** — `make deptrac` violations 0 |

### 制御の要点（面談で aidlc-docs を先に見せる前提の事実）

1. 意図的負債 → Reverse Engineering → questions 承認 → Red の順を守った（vibe coding 回避）
2. 受入ケース表（H/C/R/Q）を正本のままテスト化し、Green は表どおりのみ
3. AI 草案の却下例: After を Laravel にする案（Step 1 Q11）
4. レガシー D5（仮押さえ上書き）等は `anti-patterns.md` に観測済み → Domain の Q5 拒否で解消方向

## v1 仕上げ: Application / Infrastructure / After 画面（2026-07-24）

- **AI に任せた**: UseCase、MySQL 永続化、composition root（`after/`）、After 画面、After 用シード
- **自分が判断**: Domain は非変更のまま Step 1–3 の正本に閉じ込める。画面は表示と入力に留め、判断ロジックを Presentation に置かない
- **重かった / 迷った**: —

### Intent Done との対応（2026-07-24・After 実装後）

| # | 条件 | 状態 |
|---|------|------|
| 1 | Before / After 同一リポで比較 | **済** — `legacy/`（8080）と `after/` + `src/`（8081） |
| 2 | スコープ 4 点を受入テストで証明 | **済** — Domain Unit 17 ケース |
| 3 | decision-log に人間の核心判断 | **済** — Step 1 で Laravel 却下ほか |
| 4 | `make setup` で主要動作再現 | **済** — After シード込み（既存ボリュームは `make reset-db`） |
| 5 | README から起動・設計が読める | **済** |
| 6 | Deptrac で Domain 境界証明 | **済** — `make deptrac` violations 0 |

**次（v1 Done 残り）**: 同時書き込み Feature（DB 防衛・Intent Q7）
