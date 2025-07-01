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
use Frgmnt\Service\AuthService;
use Frgmnt\Repository\PageRepository;
use Frgmnt\Model\Page;
use PDO;

class PageController extends BaseController
{
    private PageRepository $repo;

    public function __construct(
        Request $req,
        Response $res,
        LatteViewRenderer $view,
        PDO $db,
        AuthService $auth
    ) {
        parent::__construct($req, $res, $view, $db, $auth);
        // Repository bekommt die PDO-Connection
        $this->repo = new PageRepository($this->db);
    }

    public function listAction(): void
    {
        $pages = $this->repo->fetchAll();
        $html = $this->view->render(
            \Frgmnt\Config\Constants::DIR_TEMPLATES . 'core.latte',
            ['pages' => $pages]
        );
        $this->res->write($html);
    }


    public function editAction(): void
    {
        $id = (int) $this->req->getQuery('id');
        $page = $this->repo->fetchById($id) ?: new Page();

        if ($this->req->isAjax()) {
            // Ajax-Partial
            echo $this->view->render(
                \Frgmnt\Config\Constants::DIR_TEMPLATES . '_edit.latte',
                ['page' => $page]
            );
            return;
        }
    }
    public function saveAction(Request $req, Response $res)
    {
        $page = new Page();
        $page->setId((int) $this->req->getPost('id'));
        $page->setParentId($this->req->getPost('parent_id') ?: null);
        $page->setTitle($this->req->getPost('title'));
        $this->repo->save($page);
        $this->res->redirect('/frgmnt/pages');
    }

    private function buildTree(array $pages, ?int $parentId = null): array
    {
        $branch = [];
        foreach ($pages as $page) {
            if ($page->getParentId() === $parentId) {
                $children = $this->buildTree($pages, $page->getId());
                if ($children) {
                    $page->children = $children;
                }
                $branch[] = $page;
            }
        }
        return $branch;
    }
}
