<?php
require __DIR__ . '/../vendor/autoload.php';

$lifetime = 60 * 60 * 24 * 365 * 10;
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();


use Frgmnt\Engine\Routing;

$routing = new Routing();

$routing->route();