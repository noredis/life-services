.PHONY: up build stop down down-volumes migrate-identity migrate-marketplace-profile logs-identity logs-marketplace-profile

up:
	@docker compose up -d

build:
	@docker compose up --build -d

stop:
	@docker compose stop

down:
	@docker compose down

down-volumes:
	@docker compose down -v

migrate-identity:
	@docker exec -it identity-php-fpm php bin/console doctrine:migrations:migrate

migrate-marketplace-profile:
	@docker exec -it marketplace-profile-php-fpm php bin/console doctrine:migrations:migrate

logs-identity:
	@docker exec -it identity-php-fpm tail var/log/dev.log

logs-marketplace-profile:
	@docker exec -it marketplace-profile-php-fpm tail var/log/dev.log
