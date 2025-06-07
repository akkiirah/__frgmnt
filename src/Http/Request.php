<?php

namespace Frgmnt\Http;

class Request
{
    private array $get;
    private array $post;
    private array $server;

    public function __construct(array $get = null, array $post = null, array $server = null)
    {
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->server = $server ?? $_SERVER;
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function getUri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        return strtok($uri, '?');
    }

    public function getQuery(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function getPost(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }
}
