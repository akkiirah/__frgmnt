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


namespace Frgmnt\Service;

use Frgmnt\Config\Constants;
use PDO;

/**
 * Database provides a singleton PDO connection configured via .env.php.
 */
class Database
{
    private static ?PDO $pdo = null;

    /**
     * Get or create the PDO connection.
     *
     * @return PDO
     * @throws \PDOException on connection error
     */
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8',
                Constants::get('db.host'),
                Constants::get('db.name')
            );
            self::$pdo = new PDO(
                $dsn,
                Constants::get('db.user'),
                Constants::get('db.pass'),
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$pdo;
    }
}