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

use Frgmnt\Config\Constants;

/**
 * Provides base functionality for rendering views using the Latte templating engine.
 *
 * Initializes the Latte engine with custom filters,
 * sets the temporary directory for caching and defines the file paths for different views.
 *
 * @package View
 */
abstract class AbstractViewRenderer
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
        $this->latte->setTempDirectory(Constants::DIR_CACHE);
        $this->fileStart = Constants::DIR_TEMPLATES . 'start.latte';
        // $this->fileList = Constants::DIR_TEMPLATES . 'list.latte';
        // $this->fileDetail = Constants::DIR_TEMPLATES . 'detail.latte';
        // $this->fileLogin = Constants::DIR_TEMPLATES . 'login.latte';
        // $this->fileRegister = Constants::DIR_TEMPLATES . 'register.latte';
    }
}