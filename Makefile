.PHONY: help setup up down logs ps reset-db composer-install test

help:
	@echo ""
	@echo "ticket-hold-legacy-replace"
	@echo "=================================================="
	@echo "  make setup            Docker 起動（レガシー Before）"
	@echo "  make up               コンテナ起動"
	@echo "  make down             停止"
	@echo "  make reset-db         DB ボリューム削除して再 seed"
	@echo "  make logs             ログ"
	@echo "  make ps               状態"
	@echo "  make composer-install After 依存インストール"
	@echo "  make test             PHPUnit（Domain Unit・Red/Green）"
	@echo ""
	@echo "Legacy UI: http://localhost:8080/"
	@echo ""

setup: up
	@echo ""
	@echo "=== setup complete (legacy Before) ==="
	@echo "Open http://localhost:8080/"
	@echo "After tests: make composer-install && make test"

up:
	docker compose up -d --build db legacy

down:
	docker compose down

reset-db:
	docker compose down -v
	docker compose up -d --build db legacy

logs:
	docker compose logs -f

ps:
	docker compose ps

composer-install:
	docker compose run --rm php composer install

test:
	docker compose run --rm php vendor/bin/phpunit
