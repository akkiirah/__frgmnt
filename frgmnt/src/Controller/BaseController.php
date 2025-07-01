<?php

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
use \PDO;
use Frgmnt\Service\AuthService;
use Frgmnt\View\LatteViewRenderer;

abstract class BaseController
{
    protected Request $req;
    protected Response $res;
    protected LatteViewRenderer $view;
    protected PDO $db;
    protected AuthService $auth;

    public function __construct(
        Request $req,
        Response $res,
        LatteViewRenderer $view,
        PDO $db,
        AuthService $auth
    ) {
        $this->req = $req;
        $this->res = $res;
        $this->view = $view;
        $this->db = $db;
        $this->auth = $auth;
    }
}