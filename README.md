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
You can run the application as a docker container using docker-compose, pull the base image from my hub, or run it as a normal laravel app. Either way works

#### Run the app locally
- Clone the project using the link, the latest code is on the *master* branch
- Open it in your favourite IDE and run Composer install
- Generate the app key by running `php artisan key: generate`
- Setup your database connection in your *.env* file
- Run the migration using the command `php artisan migrate`
- Serve the application using `php artisan serve` and visit [localhost:8000/docs/api](API Documentation)

#### Run the app using docker-compose
The docker setup provides the following services
- Nginx
- PHP 8.2 app
- MySQL's latest version
- PhpmyAdmin:5.2.1-apache

##### Steps to install
- Clone or download the project using the link, the latest code is on the *master* branch
- Run `cp .env.example .env`
- Run `docker-compose build`
- Run `docker-compose up`
- Visit [localhost/docs/api](API documentation) or [your_ip_address/docs/api](API documentation)
*** Provide your mail server configs in .env. Because all accounts created must be verified before a login can be successful, and you will not be able to access any protected endpoint without login. ***
Please reach out to me if any issue

#### Run the app using the image
- Run `docker run -d -p 8000:80 suheddy/crowdfundapp:0.01`
- Visit [localhost:8000/docs/api](API Documentation) or [your_ip_address/docs/api](API Documentation)

### Run test
- Run `php artisan test`
