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


namespace Frgmnt\Http;

/**
 * Represents an HTTP request and provides methods
 * to access method, URI, parameters, and AJAX status.
 */
class Request
{
    /**
     * Get the HTTP request method.
     *
     * @return string e.g. 'GET', 'POST'
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get the current request URI.
     *
     * @return string URI path and query string
     */
    public function getUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Retrieve a query parameter by key.
     *
     * @param string $key
     * @param mixed  $default Default value if key is missing
     * @return mixed
     */
    public function getQuery(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Retrieve POST data by key.
     *
     * @param string $key
     * @param mixed  $default Default value if key is missing
     * @return mixed
     */
    public function getPost(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Determine if the request was made via AJAX.
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
