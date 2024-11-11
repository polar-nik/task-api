# Task API

RESTful API для управления задачами

# Установка

```console
docker-compose up -d
cd www/
composer i
php artisan key:generate
```

По умолчанию используется 8080 порт

# Общее

Все ответы содержат ключ (_boolean_) `success` , который является признаком успешного выполнения запроса

Документация находится http://localhost:8080/api/documentation

# Авторизация:

Для получения токена необходимо зарегистрироваться:

`http://localhost:8080/auth/sign-up`

Или авторизоваться:

`http://localhost:8080/auth/sign-in`

При успешном запросе в обоих случаях вернётся ответ

```json
{
    "success": true,
    "token": "..."
}
```

# Тестирование

Для запуска тестов выполните

```console
php artisan test
```