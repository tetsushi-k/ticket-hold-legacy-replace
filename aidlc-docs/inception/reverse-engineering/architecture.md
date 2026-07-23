# System Architecture（legacy Before）

## Overview

単一 Apache コンテナが `legacy/public` を配信し、MySQL の `seat_rows` を直接更新する。レイヤ分離なし。

```mermaid
flowchart LR
  U[Browser] --> L[legacy PHP :8080]
  L --> DB[(MySQL ticket_hold)]
```

## Components

| コンポーネント | 役割 |
|----------------|------|
| `legacy` サービス | PHP 8.3 + Apache + mysqli |
| `db` サービス | MySQL 8。`init.sql` で seed |
| Makefile | `make setup` / `up` / `down` |

## Key flows

```mermaid
sequenceDiagram
  participant U as User
  participant H as hold.php
  participant C as confirm.php
  participant R as release_expired.php
  participant DB as MySQL

  U->>H: POST seat + buyer
  H->>DB: SELECT then UPDATE status=hold
  U->>C: POST seat + buyer
  C->>DB: SELECT then UPDATE status=OK
  U->>R: GET
  R->>DB: UPDATE expired hold to free
```

## After（予定・Intent）

Presentation → Application → Domain。Infrastructure が MySQL。Deptrac で Domain → FW 依存ゼロを証明。
