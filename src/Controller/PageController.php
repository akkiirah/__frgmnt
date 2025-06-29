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

use Frgmnt\Repository\PageRepository;
use Frgmnt\View\LatteViewRenderer;
use Frgmnt\Http\Request;
use Frgmnt\Http\Response;
use Frgmnt\Service\AuthService;

class PageController
{
    private Request $request;
    private Response $response;
    private LatteViewRenderer $view;
    private PageRepository $repo;
    private AuthService $auth;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->view = new LatteViewRenderer();
        $this->repo = new PageRepository();
        // if (!$this->auth->check()) {
        //     header('Location: /core');
        //     exit;
        // }
    }

    public function listAction(): void
    {
        $flat = $this->repo->fetchAll();

        $tree = $this->buildTree($flat);

        $selectedId = $this->request->getQuery('id');
        $selectedPage = null;

        if ($selectedId) {
            $selectedPage = $this->repo->fetchById((int) $selectedId);
        }

        echo $this->view->render(\Frgmnt\Config\Constants::DIR_TEMPLATES . '/core.latte', [
            'pages' => $tree,
            'page' => $selectedPage
        ]);
    }

    public function editAction(Request $req, Response $res)
    {
        $id = (int) $req->getQuery('id');
        $page = $this->repo->fetchById($id) ?: new \Frgmnt\Model\Page();

        if ($req->isAjax()) {
            echo $this->view->render(\Frgmnt\Config\Constants::DIR_TEMPLATES . '/_edit.latte', ['page' => $page]);
            return;
        }
    }

    public function saveAction(Request $req, Response $res)
    {
        $page = new \Frgmnt\Model\Page();
        $page->id = (int) $req->getPost('id');
        $page->parent_id = $req->getPost('parent_id') ?: null;
        $page->title = $req->getPost('title');
        $this->repo->save($page);
        $res->redirect('/core/pages');
    }

    private function buildTree(array $pages, ?int $parentId = null): array
    {
        $branch = [];
        foreach ($pages as $page) {
            if ($page->parent_id === $parentId) {
                $children = $this->buildTree($pages, $page->id);
                if ($children) {
                    $page->children = $children;
                }
                $branch[] = $page;
            }
        }
        return $branch;
    }
}
