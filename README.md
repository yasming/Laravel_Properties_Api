# Properties_API

This project is an api to do filter all properties from http://grupozap-code-challenge.s3-website-us-east-1.amazonaws.com/sources/source-2.json api and give the zap properties and viva real properties according of business rule .

## Prerequisites

```
PHP >= 8.0
```

```
PHP Unit >=9.3.3
```

```
Laravel >= 8.12
```

### API Collection

https://www.getpostman.com/collections/2c44d41e1a6bf7290440

### API Swagger Documentation

https://app.swaggerhub.com/apis-docs/yasminguimaraes/PROPERTIES_API/1.0.0

### Application public link

https://properties-api-list.herokuapp.com/

### Getting Started

- After you clone the project: 

```
composer install
```

```
cp .env.example .env
```

```
php artisan key:generate
```

```
php artisan jwt:generate
```

```
php artisan migrate --seed
```

### How to run project's tests

```
php artisan test
```

### How to deploy new features

```
The deploy is automatic when a push is done to master branch, but master branch is blocked, so is need to send me a pr,
after it pass for the CI tests that is runned when a pr is created for master branch,
I can accept the PR and after merge the deploy is done automatically !
```
