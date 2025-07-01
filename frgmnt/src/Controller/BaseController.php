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
use \PDO;
use Frgmnt\Service\AuthService;
use Frgmnt\View\LatteViewRenderer;


/**
 * BaseController provides common dependencies and functionality for all controllers.
 * Injected services include HTTP request/response, view rendering, database connection, and authentication.
 */
abstract class BaseController
{
    protected Request $req;
    protected Response $res;
    protected LatteViewRenderer $view;
    protected PDO $db;
    protected AuthService $auth;

    /**
     * Construct the controller with common services.
     *
     * @param Request      $req        The current HTTP request
     * @param Response     $res        The HTTP response object
     * @param LatteViewRenderer $view  Template engine service
     * @param PDO          $db         Database connection
     * @param AuthService  $auth       Authentication service
     */
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