<?php

namespace Core\Http;

class Request
{
    /** @var array<string, mixed> */
    private array $params;

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params = [])
    {
        if (!empty($params)) {
            $this->params = $params;
        } else {
            $json = json_decode(file_get_contents('php://input'), true) ?? [];
            $this->params = array_merge($_GET, $_POST, (array)$json);
        }
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return strtok($uri, '?');
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $param): mixed
    {
        if (isset($this->params[$param])) {
            return $this->params[$param];
        }
        return null;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }
}