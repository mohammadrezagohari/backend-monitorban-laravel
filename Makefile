main_yml = -f docker-compose.yml

ROOT_DIR:=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

# If the first argument is "run"...
ifeq (run,$(firstword $(MAKECMDGOALS)))
  # use the rest as arguments for "run"
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  # ...and turn them into do-nothing targets
  $(eval $(RUN_ARGS):;@:)
endif

up:
	@echo "Upping the docker containers..."
	@docker-compose $(main_yml) up -d --remove-orphans

up_no_daemon:
	@echo "Upping the docker containers..."
	@docker-compose $(main_yml) up --remove-orphans

down:
	@echo "Downing the docker containers..."
	@docker-compose $(main_yml) down --remove-orphans

restart:
	@echo "Restarting the docker containers..."
	@docker-compose $(main_yml) restart

docker_build:
	@echo "Building the docker containers..."
	@docker-compose $(main_yml) build aravel_app --compress --no-cache --force-rm --pull --parallel

docker_push:
	@echo "Pushing the docker images..."
	@docker-compose $(main_yml) push aravel_app

docker_pull:
	@echo "Pushing the docker images..."
	@docker-compose $(all_yml) pull

art:
	@docker-compose exec aravel_app php artisan $(filter-out $@,$(MAKECMDGOALS))

__migrate_fresh_seed:
	@echo "Creating migration table if not exists..."
	@docker-compose exec aravel_app php artisan migrate
	@echo "Rollback Elasticsearch indexes..."
	@docker-compose exec aravel_app php artisan elastic:migrate:rollback
	@echo "Running migrations with seeders..."
	@docker-compose exec aravel_app php artisan db:wipe --database=pgsql_data
	@docker-compose exec aravel_app php artisan migrate:fresh --seed
	@echo "Creating Elasticsearch indexes..."
	@docker-compose exec aravel_app php artisan elastic:migrate
	@echo "Importing data..."
	@docker-compose exec aravel_app php artisan drdr:import-data

composer_install:
	@docker-compose exec aravel_app composer install

composer_update:
	@docker-compose exec aravel_app composer update

composer_autoload:
	@docker-compose exec aravel_app composer dump-autoload

test:
	@echo "Running tests..."
	@docker-compose exec aravel_app "./vendor/bin/phpunit" \
		--configuration="./phpunit.xml" \
		--process-isolation \
		--do-not-cache-result \
		--stop-on-defect \
		--stop-on-error \
        --stop-on-failure \
        --stop-on-warning \
        --stop-on-risky \
        --stop-on-incomplete \
        --fail-on-warning \
        --fail-on-risky
        #--stop-on-skipped

analyse:
	@echo "Running phpstan analyse..."
	@docker-compose exec aravel_app vendor/bin/phpstan analyse --memory-limit=-1

pint:
	@echo "Running laravel pint..."
	@docker-compose exec aravel_app vendor/bin/pint

pint_test:
	@echo "Running laravel pint..."
	@docker-compose exec aravel_app vendor/bin/pint --test

pint_dirty:
	@echo "Running laravel pint..."
	@docker-compose exec aravel_app vendor/bin/pint --dirty

cache:
	docker-compose exec aravel_app php artisan route:cache
	docker-compose exec aravel_app php artisan config:cache
	docker-compose exec aravel_app php artisan view:cache
	docker-compose exec aravel_app php artisan event:cache

clear_cache:
	docker-compose exec aravel_app php artisan cache:clear

restart_queue:
	docker-compose exec aravel_app php artisan queue:restart

clear_config:
	docker-compose exec aravel_app php artisan config:clear
	docker-compose exec aravel_app php artisan route:clear
	make restart_queue


#PRODUCTION COMMANDS
deploy:
	@echo "Upping the docker containers..."
	@docker-compose $(main_yml) up -d --remove-orphans --force-recreate

#empty
%:
    @:
