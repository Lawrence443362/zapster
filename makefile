start:
	docker-compose up -d

console:
	docker exec -it zapster-php-cli php artisan tinker

stop:
	docker-compose down --remove-orphans

build:
	docker-compose down -v --remove-orphans
	docker-compose down
	docker-compose run --rm composer cp .env.example .env
	docker-compose up --build -d
	$(MAKE) php-deps-install
	$(MAKE) php-generate-key
	$(MAKE) migrate
	docker-compose run --rm php-cli php artisan db:seed
	docker-compose run --rm php-cli php artisan storage:link --force

php-deps-install:
	docker-compose run --rm composer composer install

php-generate-key:
	docker-compose run --rm php-cli php artisan key:generate

migrate:
	docker-compose run --rm php-cli php artisan migrate
