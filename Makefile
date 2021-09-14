init: docker-down-clear \
	api-clear \
	docker-pull docker-build docker-up \
	api-init
up: docker-up
down: docker-down
restart: down up

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull --include-deps

docker-build:
	docker-compose build

api-clear:
	docker run --rm -v ${CURDIR}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/*'
	docker run --rm -v ${CURDIR}/api:/app -w /app alpine sh -c 'rm -rf vendor'

api-init: api-composer-install

api-composer-install:
	docker-compose run --rm api-php-cli composer install