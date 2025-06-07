<?php
namespace Frgmnt\Controller;

use Frgmnt\Http\Request;
use Frgmnt\Http\Response;
use Frgmnt\View\LatteViewRenderer;

/**
 * Handles public-facing pages like the home (start) page.
 */
class SiteController
{
    private Request $request;
    private Response $response;
    private LatteViewRenderer $view;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->view = new LatteViewRenderer();
    }

    /**
     * Render the start (home) page.
     */
    public function startAction(): void
    {
        $html = $this->view->render(\Frgmnt\Config\Constants::DIR_TEMPLATES . '/start.latte', []);
        $this->response->write($html);
    }
}
