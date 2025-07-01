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
use Frgmnt\View\LatteViewRenderer;
use Frgmnt\Service\AuthService;
use Frgmnt\Repository\PageRepository;
use Frgmnt\Model\Page;
use PDO;


/**
 * PageController manages CMS page CRUD operations in the admin area.
 */
class PageController extends BaseController
{
    private PageRepository $repo;

    /**
     * @param Request      $req   HTTP request
     * @param Response     $res   HTTP response
     * @param LatteViewRenderer $view  Template engine wrapper
     * @param PDO          $db    Database connection
     * @param AuthService  $auth  Authentication service
     */
    public function __construct(
        Request $req,
        Response $res,
        LatteViewRenderer $view,
        PDO $db,
        AuthService $auth
    ) {
        parent::__construct($req, $res, $view, $db, $auth);
        $this->repo = new PageRepository($this->db);
    }

    /**
     * Display list of pages in the admin interface.
     *
     * @return void
     */
    public function listAction(): void
    {
        $pages = $this->repo->fetchAll();

        $html = $this->view->render(
            \Frgmnt\Config\Constants::DIR_TEMPLATES_BACKEND . '/core.latte',
            ['pages' => $pages]
        );
        $this->res->write($html);
    }


    /**
     * Serve the page edit form. If AJAX request, return partial.
     *
     * @return void
     */
    public function editAction(): void
    {
        $id = (int) $this->req->getQuery('id');
        $page = $this->repo->fetchById($id) ?: new Page();

        if ($this->req->isAjax()) {
            echo $this->view->render(
                \Frgmnt\Config\Constants::DIR_TEMPLATES_BACKEND . '/_edit.latte',
                ['page' => $page]
            );
            return;
        }

        // Non-AJAX: Liste + aktives Page-Formular
        $pages = $this->repo->fetchAll();
        $html = $this->view->render(
            \Frgmnt\Config\Constants::DIR_TEMPLATES_BACKEND . '/core.latte',
            [
                'pages' => $pages,
                'page' => $page,
            ]
        );
        $this->res->write($html);
    }

    /**
     * Save page data submitted via POST and redirect back to page list.
     *
     * @return void
     */
    public function saveAction(): void
    {
        $page = new Page();
        // Assume setter methods exist on Page model
        $page->setId((int) $this->req->getPost('id'));
        $page->setParentId($this->req->getPost('parent_id') ?: null);
        $page->setTitle($this->req->getPost('title'));

        $this->repo->save($page);
        $this->res->redirect('/frgmnt/pages');
    }
}