<?php

namespace Frgmnt\Controller;

use Frgmnt\Service\AuthService;
use Frgmnt\View\LatteViewRenderer;
use Frgmnt\Http\Request;
use Frgmnt\Http\Response;

class AuthController
{
    public function loginAction(Request $req, Response $res)
    {
        if ($req->getMethod() === 'POST') {
            $auth = new AuthService();
            if ($auth->login($req->getPost('user'), $req->getPost('pass'))) {
                $res->redirect('/core/pages');
                return;
            }
            $res->write('Login failed');
            return;
        }
        echo (new LatteViewRenderer())->render('pages/login.latte', []);
    }
}