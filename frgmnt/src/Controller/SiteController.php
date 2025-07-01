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
use Frgmnt\View\LatteViewRenderer;
use Frgmnt\Repository\PageRepository;

/**
 * Handles public-facing pages like the home (start) page.
 */
class SiteController
{
    private Request $request;
    private Response $response;
    private LatteViewRenderer $view;
    private PageRepository $pageRepository;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->view = new LatteViewRenderer();
        $this->pageRepository = new PageRepository();
    }

    /**
     * Render the start (home) page.
     */
    public function startAction(): void
    {
        $html = $this->view->render(\Frgmnt\Config\Constants::DIR_TEMPLATES . '/start.latte', []);
        $this->response->write($html);
    }

    public function showAction(Request $request, Response $response): void
    {
        // 1) Original-URI ohne Query und ohne trailing slash
        $rawPath = parse_url($request->getUri(), PHP_URL_PATH);
        $pathWithoutTs = rtrim($rawPath, '/');       // "/about-us" statt "/about-us/"
        $slugPath = ltrim($pathWithoutTs, '/'); // "about-us"

        $repo = new PageRepository();
        $page = $repo->fetchBySlugPath($slugPath);

        if (!$page) {
            $response->setStatus(404);
            $response->write('404 Not Found');
            return;
        }

        // 2) Automatisch auf erstes Kind weiterleiten, wenn vorhanden
        $children = $repo->fetchChildren($page->getId());
        if (count($children) > 0) {
            $first = $children[0];
            $redirectUri = $pathWithoutTs . '/' . $first->getSlug(); // z.B. "/about-us/team"
            $response->redirect($redirectUri);
            // redirect() macht ein exit, hier nie weiter
        }

        // 3) Kein Kind → ganz normal rendern
        $this->pageRepository->loadContentElements($page);
        $view = new LatteViewRenderer();
        $html = $view->render(
            \Frgmnt\Config\Constants::DIR_TEMPLATES . '/page.latte',
            ['page' => $page]
        );
        $response->write($html);
    }

}
