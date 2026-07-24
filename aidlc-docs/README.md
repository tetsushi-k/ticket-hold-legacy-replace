# aidlc-docs

このリポジトリの設計判断・受入例示・プロセス証跡を置く場所。実装より先にここを固める（Never Vibe Code）。

本作は **Brownfield**（意図的レガシー → 仕様復元 → ドメイン抽出）。Greenfield の証明は [`salon-booking-ddd`](../salon-booking-ddd) が担う。

## 読み順（面談・レビュー）

1. 本 README → [`decision-log.md`](decision-log.md)（Step 7 に Done 対応表あり）
2. [`inception/intent-approval-questions.md`](inception/intent-approval-questions.md)（Step 1 正本）→ [`intent.md`](inception/intent.md) / [`ubiquitous-language.md`](inception/ubiquitous-language.md)
3. [`inception/intentional-debt-plan.md`](inception/intentional-debt-plan.md) → [`legacy/`](../legacy/)（Before）
4. [`inception/reverse-engineering/`](inception/reverse-engineering/)（仕様復元・アンチパターン）
5. Step 2–3（[`domain/`](domain/) / [`construction/acceptance-criteria.md`](construction/acceptance-criteria.md)）
6. After Domain: [`../src/Domain/`](../src/Domain/) + [`../tests/Unit/Domain/`](../tests/Unit/Domain/)（受入 1:1）
7. 品質ゲート: ルート [`deptrac.yaml`](../deptrac.yaml) / [`phpstan.neon`](../phpstan.neon) → `make check`

## 承認の正本（フェーズごと）

| Step | questions（正本） | 草案 |
|------|-------------------|------|
| 1 | `inception/intent-approval-questions.md` | `intent.md`, `ubiquitous-language.md` |
| 2 | `domain/domain-modeling-questions.md` | `domain/*.md` |
| 3 | `construction/acceptance-criteria-questions.md` | `construction/acceptance-criteria.md` |

- 書式は `.aidlc-rule-details/common/question-format-guide.md`
- チャットは「Answer を埋めた／承認する」の合図用。選択肢の正本はファイル
- `decision-log.md` は要約。Red は Step 1–3 の明示承認後

## 現状（2026-07-24）

| 文書 / 成果物 | 状態 |
|---------------|------|
| キット配置 | **済** |
| Step 1 Intent | **承認済み**（2026-07-23 22:42 JST） |
| レガシー Before（`legacy/`） | **動作確認済** |
| Reverse Engineering | **済**（`spec-restored` / `anti-patterns` / `architecture`） |
| Step 2 ドメイン | **承認済み**（2026-07-23 22:51 JST） |
| Step 3 受入例示 | **承認済み**（2026-07-24 10:55 JST） |
| Step 4–6 Red/Green/Refactor | **済**（Domain Unit 17 + `make check`） |
| Step 7 ナラティブ | **済**（2026-07-24 12:03 JST） |
| Application / Infrastructure | **未**（Intent Done 残り） |

## A 作品との役割分担

| 作品 | 型 | 証明すること |
|------|-----|--------------|
| `salon-booking-ddd` | Greenfield | DG-AIDLC の型が新規ドメインで成り立つ |
| 本作 `ticket-hold-legacy-replace` | Brownfield（別ドメイン） | 仕様不明レガシーでも同じ型が横展開できる |
