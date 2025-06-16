# 🎧 Zapster

**Zapster** — это современный медиасервис, вдохновлённый Napster, разработанный на Laravel и Docker.  
Проект включает API с возможностью работы с аудиофайлами, постами, тегами. Полностью контейнеризован для кроссплатформенной разработки.

## ⚙️ Стек технологий

-   PHP 8.4
-   Laravel 12 (L12)
-   PostgreSQL
-   Redis
-   Docker + Docker Compose

## 🧱 Архитектура контейнеров

Проект полностью работает внутри Docker:

| Контейнер          | Назначение                                     |
| ------------------ | ---------------------------------------------- |
| `zapster-nginx`    | Обслуживает HTTPS (порт 8080 → 443 внутри)     |
| `zapster-php-fpm`  | Обрабатывает HTTP-запросы Laravel              |
| `zapster-php-cli`  | Используется для artisan-команд и миграций     |
| `zapster-queue`    | Обрабатывает очередь `audio_compressing_queue` |
| `zapster-composer` | Установка зависимостей Laravel                 |
| `zapster-postgres` | PostgreSQL база данных                         |
| `zapster-redis`    | Хранилище задач и кэш Redis                    |

## 🚀 Установка и запуск проекта
Проект использует Makefile для упрощения работы с docker-compose и командами Laravel. Вы можете как полностью развернуть проект одной командой, так и выполнить шаги поэтапно вручную. Ниже описаны доступные команды.

## ⚙️ Команда `make build` — Полное развёртывание проекта

Эта команда используется для полного разворачивания Laravel-проекта в Docker-среде. Она включает очистку старого окружения, сборку, установку зависимостей, генерацию ключа, миграции, сиды и симлинк хранилища.

Вот что делает каждая строка:

```makefile
docker-compose down -v --remove-orphans
```

🔹 Останавливает и удаляет все контейнеры **и тома данных**, включая "осиротевшие" (orphans). Это нужно для полной очистки окружения, чтобы начать с нуля.

```makefile
docker-compose down
```

🔹 Повторно выполняет остановку контейнеров, но **без удаления томов**. Это избыточно, но в некоторых случаях помогает устранить проблемы при пересборке (например, если первый `down` не отработал полностью).

```makefile
docker-compose run --rm composer cp .env.example .env
```

🔹 Копирует файл окружения `.env.example` в `.env` внутри контейнера. Это нужно, чтобы Laravel знал, какие переменные окружения использовать.

```makefile
docker-compose up --build -d
```

🔹 Собирает контейнеры (если это ещё не сделано) и запускает их в фоновом режиме. Использует флаг `--build`, чтобы пересобрать образы при необходимости.

```makefile
$(MAKE) php-deps-install
```

🔹 Выполняет команду:

```makefile
docker-compose run --rm composer composer install
```

Устанавливает зависимости Laravel из `composer.json`.

```makefile
$(MAKE) php-generate-key
```

🔹 Выполняет команду:

```makefile
docker-compose run --rm php-cli php artisan key:generate
```

Генерирует новый `APP_KEY` для Laravel (важно для шифрования).

```makefile
$(MAKE) migrate
```

🔹 Выполняет команду:

```makefile
docker-compose run --rm php-cli php artisan migrate
```

Применяет миграции к базе данных.

```makefile
docker-compose run --rm php-cli php artisan db:seed
```

🔹 Запускает сидеры — заполняет базу начальными данными.

```makefile
docker-compose run --rm php-cli php artisan storage:link --force
```

🔹 Создаёт символьную ссылку `storage -> public/storage`, чтобы работать с загружаемыми файлами через веб-интерфейс.

---

## 🖥 Предварительные требования

Перед запуском команды `make build`, убедитесь, что на вашей системе установлено следующее:

* **Docker** — [https://docs.docker.com/get-docker/](https://docs.docker.com/get-docker/)
* **Docker Compose** — [https://docs.docker.com/compose/install/](https://docs.docker.com/compose/install/)
* **GNU Make** — предустановлен в большинстве UNIX-систем.
  Для Windows можно использовать WSL, Git Bash или Make для Windows (например, через Chocolatey: `choco install make`).

Также:

* **Свободный порт - 8080**
* **Оперативная память от 4 ГБ** (Laravel + Docker + база данных могут потреблять ресурсы)
* **Файл `.env.example` присутствует в корне проекта** — он копируется в `.env`

---




