<?php

namespace KPG\Learnplaces\api\Core;

use RepositoryObject\Learnplaces\classes\api\Core\Response;

class Request
{
    private string $path = "/api";
    private array $routes = [];

    public function __construct()
    {
        require_once('public/Customizing/plugins/Repository/RepositoryObject/Learnplaces/classes/api/routes.php');
    }

    private function add(
        string $pattern,
        string $namespace,
        string $handler,
        string $http_method,
        string $auth_mode
    ): void {
        $pattern = preg_replace('/:([\w-]+)/', '(?<$1>[^/]+)', $pattern);
        $this->routes[] = [
            'pattern' => "#^$pattern$#",
            'namespace' => $namespace,
            'handler' => $handler,
            'http_method' => $http_method,
            'auth_mode' => $auth_mode
        ];
    }

    public function route(string $auth_mode): void
    {
        $requestedUri = urldecode(str_replace($this->path, '', explode('?', $_SERVER['REQUEST_URI'])[0]));
        foreach ($this->routes as $route) {
            if (preg_match(
                    $route['pattern'], $requestedUri, $matches
                ) && $route['http_method'] == $_SERVER['REQUEST_METHOD']) {

                if($auth_mode != $route['auth_mode']) {
                    Response::send(401, 'WRONG_AUTH_MODE');
                }
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                $this->callHandler($route['handler'], $route['namespace'], $params);
                return;
            }
        }
        Response::send(404, 'NOT_FOUND');
    }

    private function callHandler(string $handler, string $namespace, array $params): void
    {
        list($controller, $action) = explode('@', $handler);
        $controllerName =  $namespace . "\\" . $controller;
        $obj_Controller = new $controllerName();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $requestBody = [];
        } elseif (!array_key_exists('CONTENT_TYPE', $_SERVER)) {
            $requestBody = file_get_contents("php://input");
        } else {
            $rawBody = file_get_contents("php://input");
            $requestBody = json_decode($rawBody, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                Response::send(
                    400, 'JSON_STRING_BROKEN',
                    ['rawInput' => $rawBody, 'error' => json_last_error_msg()]
                );
            }
        }
        $obj_Controller->$action($params, (array) $requestBody);
    }

}