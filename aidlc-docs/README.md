# aidlc-docs

このリポジトリの設計判断・受入例示・プロセス証跡を置く場所。実装より先にここを固める（Never Vibe Code）。

本作は **Brownfield**（意図的レガシー → 仕様復元 → ドメイン抽出）。Greenfield の証明は [`salon-booking-ddd`](../salon-booking-ddd) が担う。

## 読み順（面談・レビュー）

1. 本 README → `decision-log.md`
2. `inception/intent-approval-questions.md`（Step 1 正本）→ `intent.md` / `ubiquitous-language.md`
3. `inception/intentional-debt-plan.md` → `legacy/`（Before）
4. `inception/reverse-engineering/`（仕様復元・アンチパターン）
5. Step 2–3（domain / acceptance）→ After 実装

## 承認の正本（フェーズごと）

| Step | questions（正本） | 草案 |
|------|-------------------|------|
| 1 | `inception/intent-approval-questions.md` | `intent.md`, `ubiquitous-language.md` |
| 2 | `domain/domain-modeling-questions.md` | `domain/*.md` |
| 3 | `construction/acceptance-criteria-questions.md` | `construction/acceptance-criteria.md` |

- 書式は `.aidlc-rule-details/common/question-format-guide.md`
- チャットは「Answer を埋めた／承認する」の合図用。選択肢の正本はファイル
- `decision-log.md` は要約。Red は Step 1–3 の明示承認後

## 現状（2026-07-23）

| 文書 | 状態 |
|------|------|
| キット配置 | **済** |
| Step 1 Intent | **承認済み**（22:42 JST） |
| レガシー Before（`legacy/`） | **動作確認済** |
| Reverse Engineering | **済**（`spec-restored` / `anti-patterns` / `architecture`） |
| Step 2 ドメイン | **承認済み**（22:51 JST） |
| Step 3 受入例示 | **questions 提示（未承認）** |
| After / Red–Green | **未着手**（Step 3 承認後） |

## A 作品との役割分担

| 作品 | 型 | 証明すること |
|------|-----|--------------|
| `salon-booking-ddd` | Greenfield | DG-AIDLC の型が新規ドメインで成り立つ |
| 本作 `ticket-hold-legacy-replace` | Brownfield（別ドメイン） | 仕様不明レガシーでも同じ型が横展開できる |
