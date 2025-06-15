start:
	docker compose up --build -d
console:
	php artisan tinker
stop:
	docker compose down
build:
	make start
	make php-deps-install
	make php-generate-key
	make migrate
    docker compose exec zapster-php-cli php artisan db:seed
    docker compose exec zapster-php-cli php artisan storage:link
php-deps-install:
	docker compose exec zapster-composer composer install
php-generate-key:
	docker compose exec zapster-php-cli php artisan key:generate
migrate:
	docker compose exec zapster-php-cli php artisan migrate
test:
	docker compose exec zapster-php-cli php artisan test
