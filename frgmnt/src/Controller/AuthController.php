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


namespace Frgmnt\Controller;

use Frgmnt\Http\Request;
use Frgmnt\Http\Response;
use Frgmnt\Service\AuthService;

/**
 * AuthController handles user authentication.
 */
class AuthController extends BaseController
{
    /**
     * Display the login form or process login credentials.
     *
     * @return void
     */
    public function loginAction(): void
    {
        if ($this->req->getMethod() === 'POST') {
            $username = $this->req->getPost('user');
            $password = $this->req->getPost('pass');

            if ($this->auth->login($username, $password)) {
                $this->res->redirect('/frgmnt/pages');
                return;
            }

            $this->res->write('Login failed');
            return;
        }

        // Render login template
        $html = $this->view->render(
            \Frgmnt\Config\Constants::DIR_TEMPLATES_BACKEND . 'login.latte',
            []
        );
        $this->res->write($html);
    }
}