<?php
/*
|--------------------------------------------------------------------------
| Init The Application
|--------------------------------------------------------------------------
*/
define('APP_PATH', __DIR__.'/../app');

// Include helpers.php
require APP_PATH.'/core/Helpers.php';
require __DIR__.'/../vendor/autoload.php';

// Load Dotenv
(new Dotenv\Dotenv(__DIR__.'/../'))->load(true);

/*
|--------------------------------------------------------------------------
| Set System Configuration
|--------------------------------------------------------------------------
*/
app()->set(require APP_PATH.'/config/app.php');

/*
|--------------------------------------------------------------------------
| System autoload start
|--------------------------------------------------------------------------
*/
// controllers
app()->path(app()->get('flight.controllers.path'));

// middlewares
app()->path(app()->get('flight.middlewares.path'));

// models
app()->path(app()->get('flight.models.path'));

// core
app()->path(app()->get('flight.core.path'));
/*
|--------------------------------------------------------------------------
| System autoload end
|--------------------------------------------------------------------------
*/
