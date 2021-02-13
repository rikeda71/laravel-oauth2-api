# laravel-oauth2-api

## Setup

1. execute following commands

```shell
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan migrate
docker-compose exec app php artisan passport:install
```

2. setup development settings with PHPStorm while referring [this page](https://re-engines.com/2019/06/26/laradock-phpstorm-xdebug/)

:warning: important points :warning:

- Languages & Frameworks > PHP
  - CLI Interpreter
    - Server: docker-compose
    - Service: app
  - Docker container
    - **no need settings**
  - Servers
    - Port: 8080
    - Absolute path on the server: **no need settings** 
- Run/Debug Configurations
  - IDE key: PHPStorm

3. setup environment variables

```sh
cp .env .env.local
# fill in these values
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_CALLBACK_URL=
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_CALLBACK_URL=
```
