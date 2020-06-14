<?php

namespace core;

class Router
{

    private $uri;
    private $routeFound = false;
    private $requestMethod;
    private $namespace;
    private $args = array();

    public function __construct($uri, $requestMethod)
    {
        $this->uri = trim($uri, "/");
        $this->requestMethod = $requestMethod;
    }

    public function group($prefix, $namespace, $routes){
            $prefix = trim($prefix, '/');
            if (str_contains($this->uri, $prefix) && !$this->routeFound){
                $this->namespace .= '\\'. trim($namespace,'/');

                $tempUri = $this->uri;
                //Removing the prefix from uri
                $uri = str_replace($prefix, '', $this->uri);
                $uri = trim(str_replace('//', '/', $uri), '/');
                $this->uri = $uri;

                $routes();

                //After executes the routes of the group will reset namespace and uri to his original.
                $this->namespace = '';
                $this->uri = $tempUri;
            }
    }



    public function get($route, $controllerAction)
    {

        if (!$this->isCorrectRoute($route)  || strtoupper($this->requestMethod) !== 'GET' || $this->routeFound){
            return;
        }

        $this->callFunction($controllerAction, $this->args);
    }

    public function post($route, $controllerAction)
    {

        if (!$this->isCorrectRoute($route) || strtoupper($this->requestMethod) !== 'POST' || $this->routeFound)
            return;

        $postRequestBody = file_get_contents("php://input");

        $args = array_merge(array('request' => sizeof($_POST) > 0 ? $_POST : $postRequestBody), $this->args);

        $this->callFunction($controllerAction, $args);
    }

    public function delete($route, $controllerAction)
    {
        if (!$this->isCorrectRoute($route)  || strtoupper($this->requestMethod) != 'DELETE' || $this->routeFound)
            return;


        $this->callFunction($controllerAction, $this->args);
    }

    public function put($route, $controllerAction)
    {
        if (!$this->isCorrectRoute($route) ||strtoupper($this->requestMethod) != 'PUT' || $this->routeFound)
            return;

        parse_str(file_get_contents("php://input"),$putRequestBody);

        $args = array_merge(array('request' => $putRequestBody), $this->args);

        $this->callFunction($controllerAction, $args);
    }

    public function dispatch(){
        if (!$this->routeFound){
            throw new \Exception('Route not found', 404);
        }
    }

    private function callFunction($controllerAction, $args){
            $controller = explode('@', $controllerAction)[0];
            $action = explode('@', $controllerAction)[1];
            $controller = $this->namespace . '\\' . $controller;
            $this->routeFound = true;
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