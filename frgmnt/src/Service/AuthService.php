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

use Frgmnt\Service;

/**
 * AuthService handles user authentication and session management.
 */
class AuthService
{
    /**
     * Attempt to log in a user with username and password.
     *
     * @param string $user Username
     * @param string $pass Password
     * @return bool True on successful login, false otherwise
     */
    public function login(string $user, string $pass): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE username = ?');
        $stmt->execute([$user]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($pass, $hash)) {
            return false;
        }

        // On success, store user identity in session
        $_SESSION['user'] = $user;
        return true;
    }

    /**
     * Check if a user is currently authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Log out the current user by clearing the session.
     *
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }
}