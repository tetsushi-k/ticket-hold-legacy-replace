.PHONY: help setup up down logs ps reset-db composer-install test phpstan deptrac check

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
	@echo "  make test             PHPUnit"
	@echo "  make phpstan          静的解析（src/）"
	@echo "  make deptrac          レイヤ境界検証"
	@echo "  make check            test + phpstan + deptrac"
	@echo ""
	@echo "Legacy UI: http://localhost:8080/"
	@echo ""

setup: up
	@echo ""
	@echo "=== setup complete (legacy Before) ==="
	@echo "Open http://localhost:8080/"
	@echo "After: make composer-install && make check"

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

phpstan:
	docker compose run --rm php vendor/bin/phpstan analyse -c phpstan.neon

deptrac:
	docker compose run --rm php vendor/bin/deptrac analyse --config-file=deptrac.yaml

check: test phpstan deptrac
