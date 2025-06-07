<?php

namespace Frgmnt\Controller;

use Frgmnt\Service\UserService;
use Frgmnt\View\LatteViewRenderer;

/**
 * Handles user-related actions including login, registration, and logout.
 *
 * This controller utilizes:
 * - UserService to retrieve and fetch user data.
 * - LatteViewRenderer to render the appropriate views.
 *
 * @package Controller
 */
class UserController
{
    protected ?UserService $userService;
    protected ?LatteViewRenderer $frontendViewhelper;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->frontendViewhelper = new LatteViewRenderer();
    }

    /**
     * Logs in a user using provided credentials.
     *
     * On successful login, the session is regenerated, user data is stored,
     * and the user is redirected to the homepage. If login fails, an error message is displayed.
     *
     * @param array{username: string, password: string} $params
     * @return void 
     */
    public function loginAction(array $params): void
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->userService->loginUser($params);

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                header('Location: /');
                exit;
            } else {
                $error = 'Invalid logindata. Please try again.';
            }
        }

        $templateParams = [
            'msg' => $error,
            'user' => $_SESSION['user'] ?? null,
            'action' => __FUNCTION__
        ];

        $this->frontendViewhelper->renderLogin($templateParams);
    }

    /**
     * Registers a new user with the provided data.
     *
     * On successful registration, the session is regenerated, user data is stored,
     * and the user is redirected to the homepage. If registration fails, an error message is displayed.
     *
     * @param array{username: string, email: string, password: string, confirm_password: string} $params
     * @return void
     */
    public function registerAction(array $params): void
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->userService->registerUser($params);

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                header('Location: /');
                exit;
            } else {
                $error = 'Invalid input. Please try again.';
            }
        }

        $templateParams = [
            'msg' => $error,
            'user' => $_SESSION['user'] ?? null,
            'action' => __FUNCTION__
        ];

        $this->frontendViewhelper->renderRegister($templateParams);
    }

    /**
     * Logs out the current user.
     *
     * This method destroys the session and redirects the user to the homepage.
     *
     * @param array<string, mixed> $params Not used.
     * @return void
     */
    public function logoutAction(array $params): void
    {
        session_destroy();
        header('Location: /');
        exit;
    }
}
