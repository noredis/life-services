.PHONY: lint lint-fix up down

lint:
	@vendor/bin/phpcs --standard=ruleset.xml --extensions=php --tab-width=4 -sp src

lint-fix:
	@vendor/bin/phpcbf --standard=ruleset.xml --extensions=php --tab-width=4 -sp src

up:
	@docker compose up --build -d

down:
	@docker compose down
