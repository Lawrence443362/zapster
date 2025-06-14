start:
	docker compose up --build -d
    # compose run dev
    # php artisan serve

stop:
	docker compose down

# Команда для первого запуска приложения
build:
	make start
	make php-deps-install
	make php-generate-key
	make assets-install
	make assets-build
	make migrate
    # php artisan db:seed

php-deps-install:
	docker compose exec composer composer install

php-generate-key:
	docker compose exec php-cli php artisan key:generate

migrate:
	docker compose exec php-cli php artisan migrate

test:
	docker compose exec php-cli php artisan test

assets-install:
	docker-compose exec node-cli npm install

assets-build:
	docker-compose exec node-cli npm run build


# ------------

generate_doc_for_models:
    php artisan ide-helper:models
