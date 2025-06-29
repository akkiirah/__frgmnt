<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


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