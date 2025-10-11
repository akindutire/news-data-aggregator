<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Requirements

-   Data aggregation and storage: Implement a backend system that fetches articles from selected data sources
    (choose at least 3 from the provided list) and stores them locally in a database. Ensure that the data is regularly
    updated from the live data sources.
-   API endpoints: Create API endpoints for the frontend application to interact with the backend. These endpoints
    should allow the frontend to retrieve articles based on search queries, filtering criteria (date, category, source), and
    user preferences (selected sources, categories, authors).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## App Design

[App thought process on excalidraw](https://excalidraw.com/#json=i3gfvLLLY0KisGEnNoaI8,qhyYemZWVOvYKeFeafCa1w)

## Run aggregator

For live updates - hourly

```
php artisan queue:work --queue=default,high,low

```

OR
Trigger manually - First fetch

```
php artisan aggregate:news

```

## Run project

```
php artisan migrate
php artisan serve --port 8700

```

## Supported Sources

-   NewsAPI.org
-   Guardian
-   NewYorkTimes
-   BBC Via NewsAPI.org

## Unit Test cases

Create .env.testing in project root

```
php artisan test tests/Unit/GuardianSourceUnitTest.php
php artisan test tests/Unit/NewsAPIOrgSourceUnitTest.php
php artisan test tests/Unit/NewYorkTimesSourceUnitTest.php

```

## Endpoints

-   [GET] {{base_url}}/news/category
-   [GET] {{base_url}}/news

## Sample news fetch

-   [GET] {{base_url}}/news?from=20251007&category=food,politics,footbal&source=newyorktimes&to=20251010&author=Nyt+Cooking,Sue+Li

## Supported query parameters

-   from
-   to
-   category
-   author
-   title
-   page
-   perPage
-   source (newyorktimes,newsapiorg,guardian)
-   orderBy
