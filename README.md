# Laravel API Quotes ![Stable](https://img.shields.io/badge/stable-3.3.2-blue) ![Status](https://img.shields.io/badge/status-passing-green) ![Passing](https://img.shields.io/badge/build-passing-green) ![Docker build](https://img.shields.io/badge/docker_build-passing-green)  ![Tests](https://img.shields.io/badge/tests-100%25-green) [![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=martin-stepwolf_laravel8-api-quotes&metric=sqale_index)](https://sonarcloud.io/summary/new_code?id=martin-stepwolf_laravel8-api-quotes)

_Main portfolio as PHP Backend Developer - Community to rate quotes_

## Project goal

**2021**: Personal project to apply knowledge about API REST and learn more about Laravel.

**2022**: Main portfolio to apply some experience as define business logic, apply best practices, integrate packages and try out tools.

### Achievements 2021 :star2:

- Learned better practices about APIs (versioning, url names and Authentication by API tokens).
- Implemented Authentication functions (register and login) and [Authorization - Policies](https://laravel.com/docs/8.x/authorization).
- Implemented [API resources](https://laravel.com/docs/8.x/eloquent-resources) to transform data.
- Implemented a feature where users can rate quotes (**Polymorphic relationships**).
- Tested with PHPUnit (**Test-Driven Development**) and [Postman](https://www.postman.com/) and created a documentation [link](https://documenter.getpostman.com/view/14344048/TWDUrJfS).
- Implemented custom errors and logs about information of the policy, authentication and an [Observer](https://laravel.com/docs/8.x/eloquent#observers) in Quotes.
- Create a custom [Artisan command](https://laravel.com/docs/8.x/artisan) to send an email and tested it in local ([mailhog](http://localhost:8025)).
- Implemented [Task Scheduling](https://laravel.com/docs/8.x/scheduling) to refresh the database each month and sending an email to users weekly.
- Implemented a [Listener and an Event](https://laravel.com/docs/8.x/events) to sending an email to the user when one of his quotes were rated.
- Implemented [Queue and Jobs](https://laravel.com/docs/8.x/queues) with the container Redis.
- Implemented support for spanish language (messages and emails).

### Achievements 2022 :star2:

- Created a **[Project definition](./project-definition.md)** file to define the main business logic
- Implemented **Localization and Middleware** to define language user
- Implemented **Continuous Integration** with **GitHub Actions**
- Implemented **PHP CS Fixer** to fix code standard
- Implemented **PHPStan** for a static analysis in the code
- Implemented **rector** for handle automated refactorings
- Implemented **PHP Insights** to check code quality
- Implemented **State pattern and Enums**
- Upgraded to **PHP 8.1** and **Laravel 9**
- Refactoring all the code (setUps implementation in test, use of Actions, DTOs, type hinting, etc.)
- Implemented a **QueryBuilder library** and improve the **API logic** (delete unnecessary API versioning)
- Implemented **Sonar Cloud to reduce Technical Debt** (duplications, smells code, etc.)
- Implemented some **recommendations to build large-than-average web applications**
- Reached the highest level in PHPStan, level 9 for src and level 8 for tests
- **Redefined business logic in ratings for a better scalability**
- Improved test suit with advanced practices (test command, jobs, middleware, etc.)

### Achievements 2023 :star2:

- Upgraded to **Laravel 10**
- TODO: Upgrade to PHPUnit 10

---

## Getting Started :rocket:

These instructions will get you a copy of the project up and running on your local machine.

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
 composer install
```

Then create the next alias to run commands in the container with Laravel Sail.

```
alias sail='bash vendor/bin/sail'
```

Note: Setting this alias as permanent is recommended.  

Create the images and run the services (laravel app, mysql, redis and mailhog):

```
sail up
```

With Laravel Sail you can run commands as docker-compose (docker-compose up -d = sail up -d) and php(e.g php artisan migrate = sail artisan migrate). To run Composer, Artisan, and Node / NPM commands just add sail at the beginning (e.g sail npm install). More information [here](https://laravel.com/docs/8.x/sail).

Then generate the application key.

```
sail artisan key:generate
```

Finally, generate the database with fake data:

```
sail artisan migrate --seed
```

Note: You could refresh the database any time with `migrate:refresh`.

And now you have all the environment in the port 80 (http://localhost/).

---

## Testing

### Static Analysis

In order to find errors in the code without running it. It is better before running the tests.

```
sail composer phpstan
```


### Backend testing

There are some unit testing in Models and Traits and some feature testings in controllers, all these tests guarantee functionalities like table relationship, validations, authentication, authorization, actions as create, read, update and delete, etc. 

```
sail artisan test
```

### Fix Code Standard

After we are sure our code passes the analysis and tests, before we commit it, we ensure it has the standard of code style. This should look like just one programmer has written it.

```
sail composer php-cs-fixer
```

This command fits the code automatically.

---

### Automated Refactoring

There are some rules to refactoring the code, as code quality, dead code and standard PHP 8.

```
sail composer rector
```

This is not a required step, it is an extra improving by rector.php.

---

### Analyze the code quality

There are some rules in `config/insights.php` to check the code quality.

```
sail artisan insights
```

---

## Advanced features

### Running Artisan Commands

There is a custom command to send an email as example to the users.

```
sail artisan send:newsletter
```

In docker-compose there is a container about MailHog, this container shows the email sent in your local in the port 8025 by default.

### Running Tasks Scheduled

There are two task, one to refresh the database (monthly) and other to email users (weekly) about how many users and quotes there are. Run it with:

```
sail artisan schedule:run
```

You could use `schedule:list` to look more information and its next schedule. 

### Running Queues

There is a Job (send a welcome email) created when a new user is registered  and there is an event to send an email when a quote is rated, both are stored in queue, to run them run:

```
sail artisan queue:listen
```

Note: Remember in production the better command is `queue:work`, [explanation](https://laravel-news.com/queuelisten).

---

### Built With 🛠️

-   [Laravel 10](https://laravel.com/docs/10.x/releases/) - PHP framework.
-   [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum) - Authentication system.
-   [Laravel Sail](https://laravel.com/docs/10.x/sail) - Docker development environment.
-   [Larastan](https://github.com/nunomaduro/larastan) - PHP tool to find errors in your code.
-   [PHP Code Standards Fixer](https://cs.symfony.com/) - PHP tool to fixe your code to follow standards.
-   [Rector](https://getrector.org/) - Instant Upgrades and Automated Refactoring of any PHP 5.3+ code.
-   [PHP Insights](https://phpinsights.com/) - The perfect starting point to analyze the code quality of your PHP projects.
-   [Laravel Data](https://spatie.be/docs/laravel-data/v2/introduction) - Powerful data objects for Laravel
-   [Laravel Query Builder v4](https://spatie.be/docs/laravel-query-builder/v4/introduction) - PHP package that allows you to filter, sort and include eloquent relations based on a request.
-   [Queueable actions in Laravel](https://github.com/spatie/laravel-queueable-action)
-   [Laravel Model State](https://spatie.be/docs/laravel-model-states/v2/01-introduction) - Advanced state support for Laravel models
-   [PEST PHP](https://pestphp.com/) - An elegant PHP Testing Framework

### Authors

-   Martín S. Campos - [mascam97](https://github.com/mascam97)

### Contributing

You're free to contribute to this project by submitting [issues](https://github.com/mascam97/laravel-api-quotes/issues) and/or [pull requests](https://github.com/mascam97/laravel-api-quotes/pulls).

### License

This project is licensed under the [MIT License](https://choosealicense.com/licenses/mit/).

### References :books:

- [Laravel Testing](https://testing-laravel.com/)
- [Laravel Beyond CRUD](https://laravel-beyond-crud.com/)
- [Laravel Advanced Course](https://platzi.com/clases/laravel-avanzado/)
- [Postman Course](https://platzi.com/clases/postman/)
- [Test Driven Development with Laravel Course](https://platzi.com/clases/laravel-tdd/)
- [Testing with PHP and Laravel Basic Course](https://platzi.com/clases/laravel-testing/)
- [API REST with Laravel Course](https://platzi.com/clases/laravel-api/)
- [API REST Course](https://platzi.com/clases/api-rest/)
- [GitHub Actions Basic Course](https://platzi.com/cursos/github-actions/)
- [Backend Architecture Practical Course](https://platzi.com/cursos/practico-backend/)
