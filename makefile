start:
	docker compose up --build -d
console:
	docker exec zapster-php-cli php artisan tinker
stop:
	docker compose down
build:
	make start
	make php-deps-install
	make php-generate-key
	make migrate
    docker exec zapster-php-cli php artisan db:seed
    docker exec zapster-php-cli php artisan storage:link
php-deps-install:
	docker exec zapster-composer composer install
php-generate-key:
	docker exec zapster-php-cli php artisan key:generate
migrate:
	docker exec zapster-php-cli php artisan migrate
test:
	docker exec zapster-php-cli php artisan test
