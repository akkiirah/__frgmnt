<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\View;

/**
 * Implements view rendering using the Latte templating engine.
 *
 * Extends the AbstractViewRenderer to provide concrete methods for rendering
 * various views such as list, start, detail, login, and register.
 *
 * @package View
 */
class LatteViewRenderer
{

    protected ?\Latte\Engine $latte = null;
    protected string $fileStart = '';
    protected string $fileList = '';
    protected string $fileDetail = '';
    protected string $fileLogin = '';
    protected string $fileRegister = '';

    /**
     * Constructs the view renderer.
     *
     * Initializes the Latte engine, adds custom filters, 
     * sets the temporary directory for cached templates,
     * and assigns file paths for various view templates.
     */
    public function __construct()
    {
        $this->latte = new \Latte\Engine;
        LatteFilterProvider::registerFilters($this->latte);
        $this->latte->setTempDirectory(path: \Frgmnt\Config\Constants::DIR_CACHE);
        $this->fileStart = \Frgmnt\Config\Constants::DIR_TEMPLATES_FRONTEND . '/start.latte';
        // $this->fileList = Constants::DIR_TEMPLATES_BACKEND. 'list.latte';
        // $this->fileDetail = Constants::DIR_TEMPLATES_BACKEND. 'detail.latte';
        // $this->fileLogin = Constants::DIR_TEMPLATES_BACKEND. 'login.latte';
        // $this->fileRegister = Constants::DIR_TEMPLATES_BACKEND. 'register.latte';
    }
    /**
     * Render a Latte template and return the output as string.
     *
     * @param string $template Path relative to the templates directory, including subfolder and extension, e.g. 'pages/login.latte'.
     * @param array  $params   Variables to pass into the template.
     * @return string          Rendered HTML.
     */
    public function render(string $template, array $params = []): string
    {
        return $this->latte->renderToString($template, $params);
    }
}
