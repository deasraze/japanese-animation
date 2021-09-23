init: init-ci #frontend-ready
init-ci: docker-down-clear api-clear \
	docker-pull docker-build docker-up \
	api-init api-test-init
#init-ci: docker-down-clear \
	api-clear frontend-clear \
	docker-pull docker-build docker-up \
	api-init frontend-init \
	api-test-init
up: docker-up
down: docker-down
restart: down up
check: lint analyze validate-schema test
lint: api-lint
analyze: api-analyze
validate-schema: api-validate-schema
test: api-test-init api-test
test-unit: api-test-unit
test-functional: api-test-init api-test-functional

update-deps: api-composer-update restart #frontend-yarn-upgrade restart

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --pull

api-clear:
	docker run --rm -v ${CURDIR}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/*'

api-init: api-composer-install api-wait-db api-migrations api-fixtures

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-composer-update:
	docker-compose run --rm api-php-cli composer update

api-wait-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

api-migrations:
	docker-compose run --rm api-php-cli bin/console doctrine:migrations:migrate --no-interaction

api-migrations-diff:
	docker-compose run --rm api-php-cli bin/console doctrine:migrations:diff

api-fixtures:
	docker-compose run --rm api-php-cli bin/console doctrine:fixtures:load --no-interaction

api-check: api-validate-schema api-lint api-analyze

api-validate-schema:
	docker-compose run --rm api-php-cli bin/console doctrine:schema:validate

api-lint:
	docker-compose run --rm api-php-cli composer lint
	docker-compose run --rm api-php-cli composer php-cs-fixer fix -- --dry-run --diff

api-cs-fix:
	docker-compose run --rm api-php-cli composer php-cs-fixer fix

api-analyze:
	docker-compose run --rm api-php-cli composer psalm -- --no-diff

api-analyze-diff:
	docker-compose run --rm api-php-cli composer psalm

api-test-init:
	docker-compose run --rm api-php-cli bin/console --env=test doctrine:database:drop --if-exists --force -n
	docker-compose run --rm api-php-cli bin/console --env=test doctrine:database:create --if-not-exists -n
	docker-compose run --rm api-php-cli bin/console --env=test doctrine:schema:update -f -n

api-test:
	docker-compose run --rm api-php-cli bin/phpunit

api-test-coverage:
	docker-compose run --rm api-php-cli composer test-coverage

api-test-unit:
	docker-compose run --rm api-php-cli bin/phpunit --testsuite=unit

api-test-unit-coverage:
	docker-compose run --rm api-php-cli composer test-coverage -- --testsuite=unit

api-test-functional:
	docker-compose run --rm api-php-cli bin/phpunit --testsuite=functional

api-test-functional-coverage:
	docker-compose run --rm api-php-cli composer test-coverage -- --testsuite=functional

frontend-clear:
	docker run --rm -v ${CURDIR}/frontend:/app -w /app alpine sh -c 'rm -rf .ready build'

frontend-init: frontend-yarn-install

frontend-yarn-install:
	docker-compose run --rm frontend-node-cli yarn install

frontend-yarn-upgrade:
	docker-compose run --rm frontend-node-cli yarn upgrade

frontend-ready:
	docker run --rm -v ${CURDIR}/frontend:/app -w /app alpine touch .ready