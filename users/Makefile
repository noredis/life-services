.PHONY: lint lint-fix db migrate dev-logs prod-logs sh dev-up dev-build dev-stop dev-down dev-down-volumes prod-up prod-build prod-stop prod-down prod-down-volumes

COMPOSE_DEV = docker-compose.dev.yml
COMPOSE_PROD = docker-compose.prod.yml

lint:
	@vendor/bin/phpcs --standard=ruleset.xml --extensions=php --tab-width=4 -sp src

lint-fix:
	@vendor/bin/phpcbf --standard=ruleset.xml --extensions=php --tab-width=4 -sp src

dev-up:
	@docker compose -f $(COMPOSE_DEV) up -d

dev-build:
	@docker compose -f $(COMPOSE_DEV) up --build -d

dev-stop:
	@docker compose -f $(COMPOSE_DEV) stop

dev-down:
	@docker compose -f $(COMPOSE_DEV) down

dev-down-volumes:
	@docker compose -f $(COMPOSE_DEV) down -v 

prod-up:
	@docker compose -f $(COMPOSE_PROD) up -d

prod-build:
	@docker compose -f $(COMPOSE_PROD) up --build -d

prod-stop:
	@docker compose -f $(COMPOSE_PROD) stop

prod-down:
	@docker compose -f $(COMPOSE_PROD) down

prod-down-volumes:
	@docker compose -f $(COMPOSE_PROD) down -v

db:
	@docker exec -it users-postgres psql -U ls-users-user -d ls-users_db

migrate:
	@docker exec -it users-php-fpm php bin/console doctrine:migrations:migrate

dev-logs:
	@docker exec -it users-php-fpm tail var/log/dev.log

prod-logs:
	@docker exec -it users-php-fpm tail var/log/prod.log

sh:
	@docker exec -it users-php-fpm bash
