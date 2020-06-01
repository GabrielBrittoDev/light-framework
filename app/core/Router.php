<?php

namespace core;

class Router
{

    private $uri;
    private $routeFounded = false;
    private $requestMethod;
    private $args = array();

    public function __construct($uri, $requestMethod)
    {
        $this->uri = trim($uri, "/");
        $this->requestMethod = $requestMethod;
    }

    public function get($route, $controllerAction)
    {
        if (!$this->isCorrectRoute($route)  || strtoupper($this->requestMethod) !== 'GET' || $this->routeFounded){
            return;
        }

        $this->callFunction($controllerAction, $this->args);
    }

    public function post($route, $controllerAction)
    {

        if (!$this->isCorrectRoute($route) || strtoupper($this->requestMethod) !== 'POST' || $this->routeFounded)
            return;

        $args = array_merge(array('request' => $_POST), $this->args);

        $this->callFunction($controllerAction, $args);
    }

    public function delete($route, $controllerAction)
    {
        if (!$this->isCorrectRoute($route)  || strtoupper($this->requestMethod) != 'DELETE' || $this->routeFounded)
            return;

        $this->callFunction($controllerAction, $this->args);
    }

    public function put($route, $controllerAction)
    {
        if (!$this->isCorrectRoute($route) ||strtoupper($this->requestMethod) != 'PUT' || $this->routeFounded)
            return;

        parse_str(file_get_contents("php://input"),$putRequestBody);

        $args = array_merge(array('request' => $putRequestBody), $this->args);

        $this->callFunction($controllerAction, $args);
    }

    private function callFunction($controllerAction, $args){
        $controller = explode('@', $controllerAction)[0];
        $action = explode('@', $controllerAction)[1];
        $this->routeFounded = true;
        call_user_func_array(array(new $controller, $action), $args);
    }

    private function isCorrectRoute($route){
        $route = trim($route, '/');
        $definedRoute = explode('/', $this->uri);
        $passedRoute = explode('/', $route);

        if (count($passedRoute) !== count($definedRoute)) {
            return false;
        }

        $args = array();

        foreach ($passedRoute as $index => $itemPassedRoute) {

            //IF startWith('{') AND endsWith('}')
            if (substr($itemPassedRoute, 0, 1) === '{' && substr($itemPassedRoute, strlen($itemPassedRoute) - 1, strlen($itemPassedRoute)) === '}') {
                $paramName = substr($itemPassedRoute, 1, strlen($itemPassedRoute) - 2);
                $args[$paramName] = $definedRoute[$index];
                continue;
            }

            if ($itemPassedRoute !== $definedRoute[$index]) {
                return false;
            }
        }

        $this->args = $args;
        return true;
    }
}
