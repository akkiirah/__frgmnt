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
use Frgmnt\Repository\PageRepository;

/**
 * SiteController serves public-facing pages including the home page and nested content pages.
 */
class SiteController extends BaseController
{
    private PageRepository $pages;

    /**
     * Initialize with common services and the page repository.
     *
     * @param Request           $req   HTTP request
     * @param Response          $res   HTTP response
     * @param LatteViewRenderer $view  Template engine
     * @param PDO               $db    Database connection
     * @param AuthService       $auth  Authentication service (unused for public pages)
     */
    public function __construct(
        Request $req,
        Response $res,
        LatteViewRenderer $view,
        PDO $db,
        AuthService $auth
    ) {
        parent::__construct($req, $res, $view, $db, $auth);
        $this->pages = new PageRepository($this->db);
    }

    /**
     * Render the home (start) page.
     *
     * @return void
     */
    public function startAction(): void
    {
        $html = $this->view->render(
            \Frgmnt\Config\Constants::DIR_TEMPLATES_FRONTEND . '/start.latte',
            []
        );
        $this->res->write($html);
    }

    /**
     * Show a nested page by its slug path. If the page has children,
     * automatically redirect to the first child.
     *
     * @return void
     */
    public function showAction(): void
    {
        // Clean URI path
        $rawPath = parse_url($this->req->getUri(), PHP_URL_PATH);
        $trimmedPath = rtrim($rawPath, '/');
        $slugPath = ltrim($trimmedPath, '/');

        // Fetch the page by full slug path
        $page = $this->pages->fetchBySlugPath($slugPath);
        if (!$page) {
            $this->res->setStatus(404);
            $this->res->write('404 Not Found');
            return;
        }

        // If page has children, redirect to first child automatically
        $children = $this->pages->fetchChildren($page->getId());
        if (!empty($children)) {
            $first = $children[0];
            $this->res->redirect($trimmedPath . '/' . $first->getSlug());
            return;
        }

        // Load and render the page template
        $html = $this->view->render(
            \Frgmnt\Config\Constants::DIR_TEMPLATES_FRONTEND . '/page.latte',
            ['page' => $page]
        );
        $this->res->write($html);
    }
}
