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
