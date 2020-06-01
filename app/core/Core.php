<?php

namespace core;

class Core
{

    /**
     * @return string
     */
    public function start()
    {
        $uri = $_GET['uri'] ?? '/';
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $route = new Router($uri, $requestMethod);
        $route->get('/', 'HomeController@index');

    }

}