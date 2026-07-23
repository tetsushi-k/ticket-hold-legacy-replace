# Adapter: PHP / Laravel 向けメモ

必須ではない。Laravel で DG-AIDLC を載せるときの目安。

## ディレクトリ例

- `app/Domain`
- `app/Application`（ユースケース）
- `app/Infrastructure`
- `app/Http`（薄い Presentation）

## 結線

- `AppServiceProvider::register` で Domain / Application の interface → Infrastructure 実装を `bind`

## 境界検証の例

- Deptrac（レイヤ依存）
- PHPStan

## テスト

- Domain Unit: FW なしで Domains のみ
- Feature: アプリ起動＋必要なら sqlite 等（実装クラスの差し替えは必須ではない）
