# Slim Framework 4 Microtemplate

This is a Slim microservice template based on the [Slim Skeleton](https://github.com/slimphp/Slim-Skeleton) template. Support for Doctrine 4 was added, along with some utility DTO parsing and .env file support with sensible default handling.

## Overview
### `app`
This directory contains all meta-ish information about the application. Here you register all DI related things, along with configuring your routes and the container itself.

### `logs`
Literally just logs from the app

### `public`
Good ole' php public directory, with .env file being parsed in index.php

### `src`
This is the main business logic directory that you will work the most in

#### `Application`
Here is your http communication layer, with custom middleware and route handlers living here. Additionally, app settings are defined here.

#### `Domain`
All abstract things such as interfaces and abstract classes should live here. For Doctrine entities, `Entity` directory should be where you put them, since this is the default directory where metadata is searched for.

#### `Infrastructure`
Directory for all services and repositories. I would recommend using Doctrine repositories and wiring them up with entity classes instead of writing your own from scratch, since they work very well in this setting.

### `tests`
Did not do anything with those. Just write good code or test in prod.

### `var`
This is where slim and doctrine cache things.

### `cli-config.php`
Doctrine CLI configuration. See `composer.json` for sample commands.

## Running the app
Before attempting to run the app, please make sure that `.env` is present in the project directory. Otherwise, the app will start throwing up errors on start.

### Running in docker-compose
You can either use standolone image with `Dockerfile`, or full setup with postgres included with `docker compose`:
1. `docker compose up` to build image and run the app
2. You can install deps locally with composer, or use the container: `docker compose run --rm -it slim composer install`
3. Use the provided migration commands or access Doctrine CLI: `docker compose run --rm -it slim composer db` (for fresh migration: `db:create`)
The application should be now accessible @ `http://127.0.0.1:8080`.

### Running locally
Would still recommend docker, but you can run the app with `composer start`. The same steps and commands apply - `composer install` to install dependencies and `composer db` for Doctrine CLI access.

