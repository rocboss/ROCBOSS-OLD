<?php
/**
 * System Configuration
 * @return array
 */
return [
    'flight.controllers.path' => dirname(__DIR__).'/controllers',
    'flight.middlewares.path' => dirname(__DIR__).'/middlewares',
    'flight.models.path' => dirname(__DIR__).'/models',
    'flight.core.path'  => dirname(__DIR__).'/core',
    // Setting url case_sensitive, default false
    'flight.case_sensitive' => filter_var(env('CASE_SENSITIVE', true), FILTER_VALIDATE_BOOLEAN),
    'flight.log_errors' => filter_var(env('LOG_ERROE', false), FILTER_VALIDATE_BOOLEAN),

    'cache.path' => dirname(__DIR__).'/storage/cache',
    'log.path' => dirname(__DIR__).'/storage/logs',

    // Middlewares
    'middlewares' => [
        'auth' => AuthMiddleware::class,
    ],

    // Jobs config
    'jobs' => require(dirname(__DIR__).'/config/jobs.php'),
];
