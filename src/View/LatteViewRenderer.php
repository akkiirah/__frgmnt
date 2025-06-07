<?php

namespace Frgmnt\View;

/**
 * Implements view rendering using the Latte templating engine.
 *
 * Extends the AbstractViewRenderer to provide concrete methods for rendering
 * various views such as list, start, detail, login, and register.
 *
 * @package View
 */
class LatteViewRenderer extends AbstractViewRenderer
{
    /**
     * Renders the list view using the provided parameters.
     *
     * @param array $params An associative array of parameters for the view.
     * @return void
     */
    public function renderList(array $params): void
    {
        $this->latte->render($this->fileList, $params);
        $output = $this->latte->renderToString($this->fileList, $params);
    }

    /**
     * Renders the start view using the provided parameters.
     *
     * @param array $params An associative array of parameters for the view.
     * @return void
     */
    public function renderStart(array $params): void
    {
        $this->latte->render($this->fileStart, $params);
        $output = $this->latte->renderToString($this->fileStart, $params);
    }

    /**
     * Renders the detail view using the provided parameters.
     *
     * @param array $params An associative array of parameters for the view.
     * @return void
     */
    public function renderDetail(array $params): void
    {
        $this->latte->render($this->fileDetail, $params);
        $output = $this->latte->renderToString($this->fileDetail, $params);
    }

    /**
     * Renders the login view using the provided parameters.
     *
     * @param array $params An associative array of parameters for the view.
     * @return void
     */
    public function renderLogin(array $params): void
    {
        $this->latte->render($this->fileLogin, $params);
        $output = $this->latte->renderToString($this->fileLogin, $params);
    }

    /**
     * Renders the register view using the provided parameters.
     *
     * @param array $params An associative array of parameters for the view.
     * @return void
     */
    public function renderRegister(array $params): void
    {
        $this->latte->render($this->fileRegister, $params);
        $output = $this->latte->renderToString($this->fileRegister, $params);
    }
}
