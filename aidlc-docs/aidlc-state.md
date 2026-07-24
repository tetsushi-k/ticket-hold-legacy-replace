# AI-DLC State

## Current Phase

Construction — Step 4 **Red**（失敗するテスト追加済み） / 次は **Green**

## Project Type

Brownfield（意図的レガシー → 仕様復元 → ドメイン抽出）

## Extension Configuration

（opt-in 拡張は未選択）

## Notes

- Step 1 承認: 2026-07-23 22:42 JST
- Step 2 承認: 2026-07-23 22:51 JST
- Step 3 承認: 2026-07-24 10:55 JST
- After: 素の PHP 8.3 + Domain/Application/Infrastructure + Deptrac
- Red: H1–H5 / C1–C5 / R1–R3 / Q1–Q4 → `tests/Unit/Domain/`（SeatInventory の業務メソッドは未実装で落ちる）
- 次: Green — `SeatInventory` の hold / confirm / releaseExpired / isAvailable を最小実装
