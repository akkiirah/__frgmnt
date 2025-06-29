<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\Http;

class Response
{
    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    public function setStatus(int $code): void
    {
        http_response_code($code);
    }

    public function write(string $content): void
    {
        echo $content;
    }
}