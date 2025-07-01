<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\Config;

class Constants
{
    public const ROOT_DIR = __DIR__ . '/../../';
    public const DIR_CACHE = self::ROOT_DIR . 'templates/cache';
    public const DIR_TEMPLATES = self::ROOT_DIR . 'templates/partials/';
    public const DIR_ENV = self::ROOT_DIR . '.env.php';
    protected static ?array $settings = null;

    public static function get(string $key)
    {
        if (self::$settings === null) {
            $envPath = self::DIR_ENV;
            if (file_exists($envPath)) {
                self::$settings = require $envPath;
            } else {
                throw new \RuntimeException("Missing .env.php configuration");
            }
        }
        return self::$settings[$key] ?? null;
    }
}