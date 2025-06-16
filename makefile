start:
	docker compose up -d
console:
	docker exec zapster-php-cli php artisan tinker
stop:
	docker compose down
build:
	docker-compose down
	docker-compose run --rm composer cp .env.example .env
	docker-compose up --build -d
	make php-deps-install
	make php-generate-key
	make migrate
	docker exec zapster-php-cli php artisan db:seed
	docker exec zapster-php-cli php artisan storage:link
	docker-compose up --build -d
php-deps-install:
	docker exec zapster-composer composer install
php-generate-key:
	docker exec zapster-php-cli php artisan key:generate
migrate:
	docker exec zapster-php-cli php artisan migrate
