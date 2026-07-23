.PHONY: help setup up down logs ps reset-db

help:
	@echo ""
	@echo "ticket-hold-legacy-replace"
	@echo "=================================================="
	@echo "  make setup     Docker 起動（レガシー Before）"
	@echo "  make up        コンテナ起動"
	@echo "  make down      停止"
	@echo "  make reset-db  DB ボリューム削除して再 seed"
	@echo "  make logs      ログ"
	@echo "  make ps        状態"
	@echo ""
	@echo "Legacy UI: http://localhost:8080/"
	@echo ""
	@echo "注: After / make test は Step 1–3 承認後に追加する"
	@echo ""

setup: up
	@echo ""
	@echo "=== setup complete (legacy Before) ==="
	@echo "Open http://localhost:8080/"
	@echo "Next: fill aidlc-docs/inception/intent-approval-questions.md"

up:
	docker compose up -d --build

down:
	docker compose down

reset-db:
	docker compose down -v
	docker compose up -d --build

logs:
	docker compose logs -f

ps:
	docker compose ps
