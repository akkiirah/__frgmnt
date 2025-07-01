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
    public function redirect(string $uri, int $status = 302): void
    {
        // HTTP-Status-Header sauber setzen
        header(sprintf('HTTP/1.1 %d %s', $status, $this->getStatusText($status)), true, $status);
        header('Location: ' . $uri);
        exit;  // Stopp direkt hier
    }

    private function getStatusText(int $status): string
    {
        $map = [
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
        ];
        return $map[$status] ?? '';
    }

    public function addHeader(string $name, string $value): void
    {
        header(sprintf('%s: %s', $name, $value));
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