### Crowd Fund RESTful Service

This application allows people in need to receive money from anyone interested in donating to the needy

### Features
- Authentication
- Donation Management
- Donation Collection

### Framework
- Laravel 10.0

### Packages/Tools
- Laravel Sanctum
- Scramble API documentation [https://scramble.dedoc.co] (Scramble API documentation).
- Docker/ Docker Compose

### Server requirements
- PHP 8.2
- MySQL

### How to Run Application
You can run the application in two ways: as a Docker container using docker-compose, or as a standard Laravel application. Both methods are valid.

#### Run the app locally
- Clone or download the project using the link, the latest code is on the *master* branch
- Open it in your favourite IDE and run `composer install`
- Run `cp .env.example .env`
- Generate the app key by running `php artisan key: generate`
- Setup your database connection in your *.env* file
- Run the migration using the command `php artisan migrate`
- Serve the application using `php artisan serve` and visit **http://localhost:8000/docs/api** for the API documentation

#### Run the app using docker-compose
The docker setup provides the following services
- Nginx:alpine
- PHP 8.3-fpm
- MySQL's latest version

##### Steps to install
- Clone or download the project using the link, the latest code is on the *master* branch
- Run `cp .env.example .env`
- ***Provide your mail server configs in .env.To access any protected endpoint, you must first verify your account. Please note that login will not be successful until your account is verified.***
- Run `docker-compose build`
- Run `docker-compose up`
- Visit **http://localhost/docs/api** for the API documentation

### Run test
- Run `php artisan test`
