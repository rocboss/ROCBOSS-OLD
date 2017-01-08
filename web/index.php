<?php

session_start();

require '../system/Roc.php';

Roc::set(
    // System infrastructure configuration
    require '../app/config/_base.php'
);
Roc::set(
    // Database configuration
    require '../app/config/_database.php'
);
Roc::set(
    // Miscellaneous configuration
    require '../app/config/_others.php'
);

// Route mapping
Roc::set('system.router', require '../app/router/config.php');

// Automatically load path
Roc::path(Roc::get('system.controller.path'));
Roc::path(Roc::get('system.model.path'));
Roc::path(Roc::get('system.libs.path'));

// Initialization
Roc::before('start', ['Bootstrap', 'init']);

Roc::start();
