<?php

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

        echo $this->view->render(\Frgmnt\Config\Constants::DIR_TEMPLATES . '/list.latte', ['pages' => $tree]);
    }

    public function editAction(Request $req, Response $res)
    {
        $id = (int) $req->getQuery('id');
        $page = $this->repo->fetchById($id) ?: new \Frgmnt\Model\Page();
        echo $this->view->render(\Frgmnt\Config\Constants::DIR_TEMPLATES . '/edit.latte', ['page' => $page]);
    }

    public function saveAction(Request $req, Response $res)
    {
        $page = new \Frgmnt\Model\Page();
        $page->id = (int) $req->getPost('id');
        $page->parent_id = $req->getPost('parent_id') ?: null;
        $page->title = $req->getPost('title');
        $page->content = $req->getPost('content');
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
