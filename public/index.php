<?php
/**
 * Batio Application Entrance
 */
require __DIR__."/../bootstrap/init.php";

app()->before('start', ['Batio', 'bootstrap']);
app()->start();
