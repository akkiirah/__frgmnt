<?php

namespace Frgmnt\Service;

use Frgmnt\Config\Constants;

class Database
{
    private static ?\PDO $pdo = null;

    public static function getConnection(): \PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . Constants::get('db.host')
                . ';dbname=' . Constants::get('db.name')
                . ';charset=utf8';
            self::$pdo = new \PDO(
                $dsn,
                Constants::get('db.user'),
                Constants::get('db.pass'),
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$pdo;
    }
}
