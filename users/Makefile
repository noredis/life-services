.PHONY: lint lint-fix up db migrate stop down logs sh

lint:
	@vendor/bin/phpcs --standard=ruleset.xml --extensions=php --tab-width=4 -sp src

lint-fix:
	@vendor/bin/phpcbf --standard=ruleset.xml --extensions=php --tab-width=4 -sp src

up:
	@docker compose up --build -d

db:
	@docker exec -it users-postgres psql -U ls-users-user -d ls-users_db

migrate:
	@docker exec -it users-php-fpm php bin/console doctrine:migrations:migrate

stop:
	@docker compose stop

down:
	@docker compose down

logs:
	@docker exec -it users-php-fpm tail var/log/dev.log

sh:
	@docker exec -it users-php-fpm bash
