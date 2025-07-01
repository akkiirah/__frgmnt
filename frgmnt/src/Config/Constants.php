<?php
declare(strict_types=1);

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
    // Base directory of the frgmnt engine (two levels up from this file)
    public const ROOT_DIR = __DIR__ . '/../../';

    // Where environment settings file resides
    public const DIR_ENV = self::ROOT_DIR . '.env.php';

    // Paths for templates and cache
    public const DIR_CACHE = self::ROOT_DIR . 'templates/cache';
    public const DIR_TEMPLATES_FRONTEND = self::ROOT_DIR . '../public/app/templates/partials';
    public const DIR_TEMPLATES_BACKEND = self::ROOT_DIR . 'templates/partials';

    // Public asset directories (within frgmnt/public)
    public const ASSET_CSS = '/frgmnt/assets/css/';
    public const ASSET_JS = '/frgmnt/assets/js/';

    // Internal storage for loaded settings
    protected static ?array $settings = null;

    /**
     * Get a configuration value from the .env.php file
     *
     * @param string $key
     * @return mixed|null
     * @throws \RuntimeException if .env.php is missing
     */
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