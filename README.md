# lab-hanly

## Build Setup

```bash
# install and launch server
$ cd infrastructure
$ mkdir -p ./docker/php/bash/psysh
$ touch ./docker/php/bash/.bash_history
$ docker-compose up -d --build
$ docker-compose exec app composer install --prefer-dist --no-interaction
$ docker-compose exec app cp .env.example .env
$ docker-compose exec app php artisan key:generate
$ docker-compose exec app php artisan passport:keys
$ docker-compose exec app php artisan migrate:fresh --seed

# docker start
$ docker-compose up -d

# docker down/stop
$ docker-compose down
# or
$ docker-compose stop

# docker login
$ docker-compose exec app bash
```

For more information, see the `infrastructure/Makefile`
