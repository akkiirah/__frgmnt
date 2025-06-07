<?php

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