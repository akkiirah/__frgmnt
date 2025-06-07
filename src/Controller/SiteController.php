<?php
namespace Frgmnt\Controller;

use Frgmnt\View\LatteViewRenderer;

class SiteController
{
    protected ?LatteViewRenderer $frontendViewhelper;

    public function __construct()
    {
        $this->frontendViewhelper = new LatteViewRenderer();
    }

    public function startAction(array $params): void
    {

        $templateParams = array_merge(['action' => __FUNCTION__, 'user' => $_SESSION['user'] ?? null,]);
        $this->frontendViewhelper->renderStart($templateParams);
    }
}