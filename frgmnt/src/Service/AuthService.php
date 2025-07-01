<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\Service;

use Frgmnt\Service;

class AuthService
{
    public function login(string $user, string $pass): bool
    {
        $stmt = Database::getConnection()
            ->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$user]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row && password_verify($pass, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            return true;
        }
        return false;
    }

    public function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }
}
