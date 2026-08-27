up:
	docker compose up -d --build

docker-down:
	docker compose down --remove-orphans

composer-install:
	docker compose exec php composer install

cs-fix:
	docker compose exec php vendor/bin/php-cs-fixer fix

cs-check:
	docker compose exec php vendor/bin/php-cs-fixer fix --diff

test:
	docker compose exec php composer test

phpstan-check:
	docker compose exec php vendor/bin/phpstan analyse
