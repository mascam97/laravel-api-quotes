# Laravel API Quotes ![Stable](https://img.shields.io/badge/stable-3.6.0-blue) ![Status](https://img.shields.io/badge/status-passing-green)  [![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=mascam97_laravel-api-quotes&metric=sqale_index)](https://sonarcloud.io/summary/new_code?id=mascam97_laravel-api-quotes)  [![Coverage](https://sonarcloud.io/api/project_badges/measure?project=mascam97_laravel-api-quotes&metric=coverage)](https://sonarcloud.io/summary/new_code?id=mascam97_laravel-api-quotes)

_Main portfolio as PHP Backend Developer - Community to rate quotes_

## Project goal

**2021**: Personal project to apply knowledge about API REST and learn more about Laravel.

**2022**: Project to apply some experience as define business logic, apply best practices, integrate packages and try out tools.

**2023**: Main portfolio to apply some advanced functionalities and practices.

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
- Reached the 8 level in PHPStan
- **Redefined business logic in ratings for a better scalability**
- Improved test suit with advanced practices (test command, jobs, middleware, etc.)

### Achievements 2023 :star2:

- Upgraded to **PHP 8.2** and **Laravel 10**
- Upgraded to **PHPUnit 10**
- Implemented an **Administrator domain**
- Implemented **Roles and Permissions** for administrator domain
- Implemented **User administration** for administrator domain
- Implemented **Activities** in user deletion for administrator domain
- Optimized **Database Queries** with best practices and include queries in tests
- Increased **Coverage** to 95%
- Implemented an **External API** to get data
- Implemented **OAuth** for authentication
- Implemented **Soft Delete** in Users
- Implemented **MySQL and Redis** in pipeline
- Improved **API design**
- Implemented **GraphQL** and use `queries` to get data

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
 composer install --ignore-platform-reqs
```

Then create the next alias to run commands in the container with Laravel Sail (for Linux)

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

Generate the database with fake data:

```
sail artisan migrate --seed
```

Finally, create the encryption keys needed to generate secure access tokens (`personal access` and `password grant`).

```
php artisan passport:install
```

Note: There are some clients generated by the seeder. To create a new one, use

```
`php artisan passport:client --password --name="Name" --provider="users"`
```

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

## Deployment 📦

### API Documentation :page_facing_up:

Run the next command to generate the API documentation in the folder `public/docs`.

```
sail artisan scribe:generate
```

### Built With 🛠️

- [Laravel 10](https://laravel.com/docs/10.x/releases/) - PHP framework.
- [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum) - Authentication system.
- [Laravel Sail](https://laravel.com/docs/10.x/sail) - Docker development environment.
- [Laravel Permission](https://spatie.be/docs/laravel-permission/v5/introduction) - Associate users with roles and permissions.
- [Laravel Activitylog](https://spatie.be/docs/laravel-activitylog/v4/introduction) - Log the activities of your users.
- [Laravel Excel](https://laravel-excel.com/) - Supercharged Excel exports and imports in Laravel.
- [Laravel Event Sourcing](https://spatie.be/docs/laravel-event-sourcing/v7/introduction) - The easiest way to get started with event sourcing in Laravel.
- [Laravel GraphQL](https://github.com/rebing/graphql-laravel) - Laravel wrapper for Facebook's GraphQL.
- [Laravel Query Detector](https://beyondco.de/docs/laravel-query-detector/usage) - Laravel N+1 Query Detector.
- [Laravel Passport](https://laravel.com/docs/10.x/passport) - Full OAuth2 server implementation.
- [Laravel Vapor](https://vapor.laravel.com/) - Serverless deployment platform for Laravel.
- [Larastan](https://github.com/nunomaduro/larastan) - PHP tool to find errors in your code.
- [PHP Code Standards Fixer](https://cs.symfony.com/) - PHP tool to fixe your code to follow standards.
- [Rector](https://getrector.org/) - Instant Upgrades and Automated Refactoring of any PHP 5.3+ code.
- [PHP Insights](https://phpinsights.com/) - The perfect starting point to analyze the code quality of your PHP projects.
- [Laravel Data](https://spatie.be/docs/laravel-data/v2/introduction) - Powerful data objects for Laravel
- [Laravel Query Builder v4](https://spatie.be/docs/laravel-query-builder/v4/introduction) - PHP package that allows you to filter, sort and include eloquent relations based on a request.
- [Queueable actions in Laravel](https://github.com/spatie/laravel-queueable-action)
- [Laravel Model State](https://spatie.be/docs/laravel-model-states/v2/01-introduction) - Advanced state support for Laravel models
- [PEST PHP](https://pestphp.com/) - An elegant PHP Testing Framework
- [MoneyPHP](https://moneyphp.org/) - PHP implementation of Fowler's Money pattern.
- [Scribe](https://scribe.knuckles.wtf/laravel/) - Generate API documentation for humans from your Laravel codebase.

### Authors

-   Martín S. Campos - [mascam97](https://github.com/mascam97)

### Contributing

You're free to contribute to this project by submitting [issues](https://github.com/mascam97/laravel-api-quotes/issues) and/or [pull requests](https://github.com/mascam97/laravel-api-quotes/pulls).

### License

This project is licensed under the [MIT License](https://choosealicense.com/licenses/mit/).

### References :books:

- [EVENT SOURCING IN LARAVEL: Videos, ebook & code](https://event-sourcing-laravel.com/)
- [20+ Laravel best practices, tips and tricks to use in 2023](https://benjamincrozat.com/laravel-best-practices)
- [OWASP API Security Project](https://owasp.org/www-project-api-security/)
- ["Cruddy by Design" - Adam Wathan - Laracon US 2017](https://www.youtube.com/watch?v=MF0jFKvS4SI)
- [Steve McDougall: How to do API integrations in Laravel](https://www.youtube.com/live/0Rq-yHAwYjQ?feature=share&t=22323)
- [18 Tips to optimize laravel database queries](https://dudi.dev/optimize-laravel-database-queries/)
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
