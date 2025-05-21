.SHELLFLAGS := -euc
DOCKER_DIR := docker

PHP_INI_DIR := $(DOCKER_DIR)/php-cli/assets

DOCKER_ENV_FILE := $(DOCKER_DIR)/.env
DOCKER_ENV_EXAMPLE := $(DOCKER_DIR)/.env.example

ifeq (,$(wildcard $(DOCKER_ENV_FILE)))
  include $(DOCKER_ENV_EXAMPLE)
  export $(shell sed 's/=.*//' $(DOCKER_ENV_EXAMPLE))
  $(shell cp -f $(DOCKER_ENV_EXAMPLE) $(DOCKER_ENV_FILE))
endif

ifneq (,$(wildcard $(DOCKER_ENV_FILE)))
  include $(DOCKER_ENV_FILE)
  export $(shell sed 's/=.*//' $(DOCKER_ENV_FILE))
endif

ifeq ($(shell uname), Darwin)
	SED_INPLACE_FLAG=-i ''
	FORCED_REWRITE_FLAG=-f
	XDEBUG_CLIENT_HOST := host.docker.internal
else
	SED_INPLACE_FLAG=-i
	FORCED_REWRITE_FLAG=--remove-destination
	XDEBUG_CLIENT_HOST := $(shell hostname -I | cut -d" " -f1)
endif

install: \
	install-docker-build \
	install-php-ini \
	install-database \
	install-app \
	install-migrations \
	start \
	demo

install-docker-build:
	@echo "Build docker images"
	cd ./docker && \
	cp $(FORCED_REWRITE_FLAG) compose.override.yml.example compose.override.yml && \
	sed $(SED_INPLACE_FLAG) "s/HOST_UID:.*/HOST_UID: $(shell id -u)/" compose.override.yml && \
	docker compose build

install-php-ini:
	@echo "Preparing php ini files" && \
	cp $(FORCED_REWRITE_FLAG) $(PHP_INI_DIR)/php.ini.example $(PHP_INI_DIR)/php.ini && \
	cp $(FORCED_REWRITE_FLAG) $(PHP_INI_DIR)/xdebug.ini.example $(PHP_INI_DIR)/xdebug.ini && \
	sed $(SED_INPLACE_FLAG) "s/XDEBUG_CLIENT_HOST/${XDEBUG_CLIENT_HOST}/" $(PHP_INI_DIR)/xdebug.ini

install-database:
	@echo "Preparing database"
	cd ./docker && \
	docker compose up -d mariadb && \
	docker run --rm --network $(COMPOSE_PROJECT_NAME)_network jwilder/dockerize -wait tcp://$(COMPOSE_PROJECT_NAME)_mariadb:3306 -timeout 45s

install-app:
	@echo "Preparing app env file"
	cp -f ./app/.env.example ./app/.env

	@echo "Install composer packages"
	cd ./docker && docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli sh -c "composer install"

install-migrations:
	@echo "Install migrations"
	cd ./docker && \
	docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli bin/console doctrine:migrations:migrate --no-interaction

start:
	cd ./docker && docker compose up -d
	@echo "Application is available at: http://127.0.0.1:8888/api/doc"

start-consumer:
		cd ./docker && \
		docker compose run --rm \
			-u www-data \
			-e XDEBUG_MODE=off \
			--name=$(COMPOSE_PROJECT_NAME)-consumer \
			php-cli \
			bin/console messenger:consume redis -vv; \

stop:
	cd ./docker && docker compose down -v

restart: stop start

stop-consumer:
	cd ./docker && \
	docker exec -it -u www-data -e XDEBUG_MODE=off \
	$(COMPOSE_PROJECT_NAME)-consumer \
	bin/console messenger:stop-workers

clean:
	cd ./docker && docker compose down -v
	git clean -fdx -e .idea

mariadb-cli:
	cd ./docker && docker compose exec mariadb mariadb -u $(DB_USER) --password=$(DB_PASS)

sh:
	cd ./docker && docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli sh -l

test:
	cd ./docker && docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli sh -c "composer qa"

demo: dispatch start-consumer

dispatch:
	cd ./docker && docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli \
		php bin/console app:log:parse data/logs.log -vv