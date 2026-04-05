# Лабораторная работа №4: Разработка плагина для WordPress

## Описание лабораторной работы

### Цель работы
Создать плагин, использующий CPT (Custom Post Type), пользовательскую таксономию, метаданные с метабоксом в админ-панели, а также реализовать вывод данных на сайте через шорткод.

### Условие
Создать учебный плагин `USM-Notes`, который добавляет в сайт раздел "Заметки" с приоритетами и датой напоминания.

## Инструкции по запуску проекта
1. Скопировать переменные окружения:
```powershell
Copy-Item .env.example .env
```
2. Проверить `.env` (`WP_HTTP_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`, `WORDPRESS_TABLE_PREFIX`).
3. Поднять контейнеры:
```powershell
docker compose up -d --build
```
4. Открыть сайт: `http://localhost:<WP_HTTP_PORT>`.
5. В админке активировать плагин `USM-Notes`.


## Ход выполнения

### 1) Подготовка среды
Включаю отладку в wp-config.
```php
if ( ! defined( 'WP_DEBUG' ) ) {
   define( 'WP_DEBUG', true );
   define('WP_DEBUG_DISPLAY', false);
   define('WP_DEBUG_LOG', true);
}
```

Источник: [Hostinger wp-debug](https://www.hostinger.com/tutorials/debug-wordpress?utm_source=google&utm_medium=cpc&utm_id=12231291749&utm_campaign=Generic-Tutorials-DSA-t1|NT:Se|LO:Other-EU&utm_term=&utm_content=685259525759&gad_source=1&gad_campaignid=12231291749&gbraid=0AAAAADMy-haF7XBkdwO8HnnGJSaC5ec1F&gclid=Cj0KCQjwkMjOBhC5ARIsADIdb3dkZhjtpkJkGKR81ZDB0IaH6eWGKHbT5SgQojfVeC2me1PCLp0hp-waAqtIEALw_wcB#h-how-to-enable-wordpress-debug-with-code)

### 2) Управление ролями и паролями
Создаю тестового пользователя с ролью "Автор".
![Пользователь Автор](<lab05_screenshots/new author user.png>)

У каждого администратора включены сложные пароли (8+ символов, буквы/цифры/символы). Сделано при помощи плагина 'Password Policy Manager', однако правила паролей распрастраняются на всех пользователей, а не только администраторов.

![Password Policy Manager](<lab05_screenshots/password policy.png>)

### 3) Обновления ядра, тем и плагинов
Проверяю наличие обновления и обнаруживаю 5 доступных обновлений включая обновления вордпресс.
Мои шаги:
- Обновляю бинари вордпресса до последней версии при помощи UI.
![Обновление бинарных файлов вордпресс](<lab05_screenshots/wp update to latest.png>)

- Отключаю докер контейнер

- Обновляю докер контейнер вордпресс:
`image: wordpress:6.8.3-php8.3-apache` -> `image: wordpress:6.9.4-php8.3-apache`


- Обновляю плагины.

- Настраиваю автоматическое обновление для тем и плагинов (Сомнительное решение).

Комментарий:
В идеале должен быть настроен пайплайн с переносом и установкой всех тем после обновления вордпресс в докере, необходимые настройки и файлы для работы должны храниться отдельно от продакшена а не только в нем, в моем же случае я просто показываю что знаю 
как обновить вордпресс и флоу который я представляю в текущей неидеальной ситуации. 