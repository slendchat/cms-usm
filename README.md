# Локальная разработка WordPress (Docker Compose)

Чистое локальное окружение WordPress + MariaDB только через Docker.

## Документы

- lab01: [Будущее_CMS_Headless‑подход,_облачные_интеграции_и_AI.pdf](docs/Будущее_CMS_Headless‑подход,_облачные_интеграции_и_AI.pdf)
- lab02: [mamaliga-artur_cms_lab02.docx](docs/mamaliga-artur_cms_lab02.docx)
- lab03: [mamaliga-artur_cms_lab03.md](docs/mamaliga-artur_cms_lab03.md)
- lab05: [mamaliga-artur_cms_lab04.md](docs/mamaliga-artur_cms_lab04.md)

## Структура репозитория

```text
.
├── .env
├── .env.example
├── .gitignore
├── codex-instructions.txt
├── docker-compose.yml
├── infra.md
├── README.md
├── docs/
└── wordpress/
    ├── sql/
    └── wordpress/
```

## Сервисы

- `wordpress`: контейнер Apache + PHP + WordPress, публикуется на хост через `WP_HTTP_PORT`.
- `db`: контейнер MariaDB, только внутренняя сеть (без проброса порта на хост).

## Конфигурация

Вся runtime-конфигурация находится в `.env`. Начните с:

```powershell
Copy-Item .env.example .env
```

Основные переменные:

- `WP_HTTP_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `DB_ROOT_PASSWORD`
- `WORDPRESS_TABLE_PREFIX`

## Первая инициализация WordPress

Если `./wordpress/wordpress` пустая, перед запуском явно инициализируйте файлы:

```powershell
docker run --rm -v "${PWD}\wordpress\wordpress:/target" wordpress:6.8.3-php8.3-apache sh -c "cp -a /usr/src/wordpress/. /target/"
```

Затем поднимите стек:

```powershell
docker compose up -d
```

Откройте: `http://localhost:<WP_HTTP_PORT>`
