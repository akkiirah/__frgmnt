<?php

namespace Frgmnt\Config;

class Constants
{
    public const ROOT_DIR = __DIR__ . '/../../';
    public const DIR_CACHE = self::ROOT_DIR . 'src/Cache/templates';
    public const DIR_TEMPLATES = self::ROOT_DIR . 'templates/partials/';

    public static function get(string $key)
    {
        $settings = [
            'db.host' => 'localhost',
            'db.user' => 'admin',
            'db.pass' => 'Djtobse777.exe',
            'db.name' => 'frgmnt',
        ];
        return $settings[$key] ?? null;
    }
}