# Деплой Laravel на Beget

Кратко: **Composer** — это менеджер зависимостей PHP (как `npm` для Node). Он читает `composer.json`, качает пакеты в `vendor/`.  
**`composer.phar`** — один файл «программа Composer», который запускают так: `php composer.phar install`. На Beget часто уже есть Composer в панели или в SSH — тогда `.phar` не обязателен.

---

## 1. Что такое `composer.phar` и зачем он

- Обычно ставят **Composer глобально** (`composer` в терминале).
- **`composer.phar`** — portable-версия в одном файле: скачали в папку проекта → вызываете `php composer.phar`.
- На хостинге удобно, если нет прав ставить глобально — кладёте `composer.phar` **вне** `public` (рядом с `artisan`), не в веб-корень.

Установка `composer.phar` на сервере (SSH, из домашней директории пользователя):

```bash
cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=$HOME --filename=composer.phar
php -r "unlink('composer-setup.php');"
php ~/composer.phar --version
```

Дальше в каталоге проекта:

```bash
cd /path/to/your/laravel-project
php ~/composer.phar install --no-dev --optimize-autoloader
```

На Beget в панели иногда есть раздел вроде **«Composer»** — можно поставить зависимости оттуда (аналог `composer install`).

---

## 2. Подготовка проекта к продакшену

Локально перед выкладкой:

- В `.env` на сервере: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://ваш-домен.ru`
- Секреты: `APP_KEY`, БД, `YOOKASSA_*`, `DADATA_*` — только на сервере, не в git.

На сервере после загрузки файлов:

```bash
cp .env.example .env
# отредактировать .env (БД, URL, ключи)
php artisan key:generate
php ~/composer.phar install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Права (типично для shared):

```bash
chmod -R u+rwX storage bootstrap/cache
```

---

## 3. Документ-рут (корень сайта) = папка `public`

Laravel должен открываться **только** через `public/index.php`, а не из корня репозитория.

На Beget:

1. Залейте **весь** проект (например в `~/laravel-app` или `~/domains/ваш-домен.ru/laravel`).
2. В настройках сайта укажите **корень документа** на каталог **`.../public`**  
   (в панели Beget: сайт → настройки → корневая директория / document root).

Альтернатива: симлинк `public_html` → `.../public` (если политика хостинга позволяет).

**Не кладите** `vendor/`, `.env`, весь проект внутрь открытого `public` без изоляции — только содержимое `public` должно быть «лицом» сайта.

---

## 4. База данных

- Создайте БД и пользователя в панели Beget.
- Пропишите в `.env`: `DB_HOST` (часто не `127.0.0.1`, а хост из панели), `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

---

## 5. HTTPS и прокси

В проекте для shared-хостинга в `TrustProxies` задано доверие прокси (`*`), чтобы корректно определялись HTTPS и URL за фронтом Beget.

---

## 6. Webhook ЮKassa

- URL webhook ЮKassa в личном кабинете:  
  `https://ваш-домен.ru/webhooks/yookassa`  
  (метод POST, без CSRF — маршрут уже в исключениях.)

---

## 7. Очереди и cron (по желанию)

Сейчас в `.env.example` стоит `QUEUE_CONNECTION=sync` — для небольшой нагрузки ок.

Планировщик Laravel (если понадобится):

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Добавляется в «Cron» в панели Beget.

---

## 8. Что не заливать в git на прод

- `.env` — только на сервере.
- `node_modules/` — если собираете фронт локально, на Beget часто достаточно текущих Blade + CDN (как у вас с Tailwind CDN).

---

## 9. Проверка после деплоя

1. Открывается главная, нет 500.
2. `php artisan migrate:status` — миграции применены.
3. Загрузка картинок: `storage:link`, файлы в `storage/app/public`.
4. Тестовый платёж + строки `yookassa.webhook` в `storage/logs/laravel.log`.

Если что-то падает — смотрите `storage/logs/laravel.log` и логи ошибок в панели Beget.
