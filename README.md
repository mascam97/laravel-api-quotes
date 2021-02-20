# Laravel8 API Quotes

_Community to share quotes._

### Project goal by martin-stepwolf :goal_net:

Personal project to apply my knowledge about API REST and learn about [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum).

### Achievements :star2:

- Learned better practices about APIs (versioning and url names).
- Created Authentication functions (register and login).
- Implemented Authentication by API tokens with Laravel Sanctum.
- Implemented [Authorization - Policies](https://laravel.com/docs/8.x/authorization).
- Implemented [API resources](https://laravel.com/docs/8.x/eloquent-resources) to transform data.
- Worked with **Test-Driven Development** with PHPUnit.
- Tested with [Postman](https://www.postman.com/) and created a documentation [link](https://documenter.getpostman.com/view/14344048/TWDUrJfS).

## Getting Started :rocket:

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites :clipboard:

The programs you need are:

-   [Docker](https://www.docker.com/get-started).
-   [Docker compose](https://docs.docker.com/compose/install/).

### Installing 🔧

First duplicate the file .env.example as .env.

```
cp .env.example .env
```

Note: You could change some values, anyway docker-compose create the database according to the defined values.

Then install the PHP dependencies:

```
 docker run --rm --interactive --tty \
 --volume $PWD:/app \
 composer require laravel/sail --dev
```

Then create the next alias to run commands in the container with Laravel Sail.

```
alias sail='bash vendor/bin/sail'
```

Create the images (laravel app and mysql) and run the services:

```
sail up
```

With Laravel Sail you can run commands as docker-compose (docker-compose up -d = sail up -d) and php(e.g php artisan migrate = sail artisan migrate). To run Composer, Artisan, and Node / NPM commands just add sail at the beginning (e.g sail npm install). More information [here](https://laravel.com/docs/8.x/sail).

Then generate the application key.

```
sail artisan key:generate
```

Finally generate the database with fake data:

```
sail artisan migrate --seed
```

Note: You could refresh the database any time with migrate:refresh.

And now you have all the environment in the port 80 (http://localhost/).

## Running the tests

To test the Routes, Controllers, Security and the functionality in general run:

```
sail artisan test
```

## Deployment 📦

For production environment you need extra configurations for security as:

Set in the file .env the next configuration.

```
APP_ENV=production
```

## Built With 🛠️

-   [Laravel 8](https://laravel.com/docs/8.x/releases/) - PHP framework.
-   [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum) - Authentication system.
-   [Laravel Sail](https://laravel.com/docs/8.x/sail) - Docker development environment.

## Authors

-   Martín Campos - [martin-stepwolf](https://github.com/martin-stepwolf)

## Contributing

You're free to contribute to this project by submitting [issues](https://github.com/martin-stepwolf/laravel8-api-quotes/issues) and/or [pull requests](https://github.com/martin-stepwolf/laravel8-api-quotes/pulls).

## License

This project is licensed under the [MIT License](https://choosealicense.com/licenses/mit/).

## References :books:

- [Postman Course](https://platzi.com/clases/postman/)
- [Test Driven Development with Laravel Course](https://platzi.com/clases/laravel-tdd/)
- [Testing with PHP and Laravel Basic Course](https://platzi.com/clases/laravel-testing/)
- [API REST with Laravel Course](https://platzi.com/clases/laravel-api/)
- [API REST Course](https://platzi.com/clases/api-rest/)
