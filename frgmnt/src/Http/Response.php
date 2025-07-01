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
 * Handles HTTP response status, headers, and output.
 */
class Response
{
    /**
     * Set the HTTP status code.
     *
     * @param int $code
     * @return void
     */
    public function setStatus(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Add an HTTP header to the response.
     *
     * @param string $name  Header name
     * @param string $value Header value
     * @return void
     */
    public function addHeader(string $name, string $value): void
    {
        header("$name: $value");
    }

    /**
     * Write content to the response body.
     *
     * @param string $content
     * @return void
     */
    public function write(string $content): void
    {
        echo $content;
    }

    /**
     * Redirect to another URI.
     *
     * @param string $uri    Target URI
     * @param int    $status HTTP status code (default 302)
     * @return void
     */
    public function redirect(string $uri, int $status = 302): void
    {
        $this->setStatus($status);
        $this->addHeader('Location', $uri);
        exit;
    }
}