# ticket-hold-legacy-replace

興行チケットの **仮押さえ → 本確定／期限切れ解放** と **二重確保拒否** を、意図的レガシーから DG-AIDLC でドメイン抽出する Brownfield 作品。

Greenfield の対照は [`salon-booking-ddd`](../salon-booking-ddd)。本作は **別ドメインで型が横展開できること** の証明。主役は [`aidlc-docs/`](aidlc-docs/README.md)。

> 設計判断の詳細・承認ゲートは [`aidlc-docs/`](aidlc-docs/README.md) を参照。

## ① 概要

| フェーズ | パス | 状態（2026-07-24） |
|----------|------|-------------------|
| Before | `legacy/` | 動作確認済（意図的負債込み） |
| After | `src/Domain/` + `tests/Unit/` | **Red**（受入 17 ケース。業務メソッド未実装） |
| プロセス | `aidlc-docs/` | Step 1–3 承認済 → Step 4 Red |

スコープ（v1）: 仮押さえ作成 / 本確定 / 期限切れ解放 / 二重確保拒否。決済・会員・検索 UI 等は入れない。

## ② 使用技術（現状）

| 層 | 選択 |
|----|------|
| Before | PHP 8.3 + mysqli + Apache（compose） |
| After | PHP 8.3（素の PHP）+ PHPUnit 11 + Composer |
| DB | MySQL 8 |
| DX | Makefile + docker compose（`php` サービスで test） |
| 境界検証 | Deptrac / PHPStan（Green 以降） |

## ③ アーキテクチャ（予定）

Before は画面ごと PHP。After は Domain / Application / Infrastructure へ抽出する（いま Red は Domain Unit のみ）。

```mermaid
flowchart LR
  subgraph before [Before legacy]
    P[hold/confirm/release PHP] --> DB[(MySQL)]
  end
  subgraph after [After]
    API[Presentation] --> App[Application]
    App --> Dom[Domain]
    Infra[Infrastructure] --> Dom
    Infra --> DB2[(MySQL)]
  end
```

## ④ 設計上の工夫

- Brownfield: 意図的負債を `intentional-debt-plan.md` に計画してから `legacy/` に埋め、Reverse Engineering で復元する
- Never Vibe Code: Step 1–3 の questions 承認後に Red（失敗テスト）→ Green
- 受入例示とテストは 1:1（`acceptance-criteria.md` の H/C/R/Q = Unit メソッド）

意図的負債の例（Before）: グローバル `$conn`、状態語のゆれ、期限チェックの抜け、仮押さえ同士の二重を許す穴。詳細は `aidlc-docs/inception/intentional-debt-plan.md`。

## ⑤ ローカル起動方法

```bash
make setup
```

Legacy UI: http://localhost:8080/

After テスト:

```bash
make composer-install
make test
```

## ⑥ 動作確認（Before）

1. 空席で「仮押さえ(A)」→ status が `hold`、`hold_until` が入る
2. 「本確定(A)」→ status が `OK`（レガシーのゆれた確定語）
3. 別購入者の「仮押さえ(B)」を同じ席に重ねると上書きできる（意図的穴 D5）
4. 「期限切れ解放を実行」で期限超過の hold を `free` に戻せる

## ⑦ ディレクトリ構成

```text
ticket-hold-legacy-replace/
├── aidlc-docs/           # 設計・判断・受入（主役）
├── legacy/               # Before
├── src/Domain/           # After ドメイン（Red: 業務未実装）
├── tests/Unit/Domain/    # 受入 H/C/R/Q の失敗テスト
├── adapters/             # DG-AIDLC スタックメモ
├── .cursor/rules/        # dg-aidlc / ai-dlc-workflow
├── .aidlc-rule-details/
├── Makefile
├── composer.json
└── docker-compose.yml
```

## ⑧ 今後の拡張案

- Green → Application / Infrastructure → Deptrac
- 同時リクエストの明示デモ用シナリオ

## AI 駆動開発（事実）

- キット `dg-aidlc` を配置し、Step 1–3 を questions 正本で承認（2026-07-23〜24）
- Step 4 Red: 受入ケース表どおりの Domain Unit を先に書いた（2026-07-24）
- 詳細は [`aidlc-docs/`](aidlc-docs/README.md) と [`decision-log.md`](aidlc-docs/decision-log.md)
