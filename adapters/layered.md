# Adapter: レイヤ配置の目安（言語非依存）

作品リポでディレクトリ名は変えてよい。役割だけ揃える。

| 役割 | 置くもの | 置かないもの |
|------|----------|--------------|
| Domain | 集約、値オブジェクト、ドメインサービス、永続化 port（interface） | FW、ORM、HTTP、SQL |
| Application / UseCase | ユースケース手順、読取用 port（必要なら）、DTO | 業務ルールの本体、SQL |
| Infrastructure | ORM、外部 API、port の実装 | ユースケースの分岐ロジック |
| Presentation | Controller、CLI、ルーティング | ドメイン判断 |

## 境界検証

- Domain → FW / ORM 依存ゼロを、アーキテクチャテストまたは依存ルールツールで証明する
- ツール名はスタックごとに作品 README に書く

## DI（composition root）

- interface → 実装の結線は起動時の1か所（Service Provider、モジュール、main 等）に閉じる
- UseCase は interface だけをコンストラクタ注入する
