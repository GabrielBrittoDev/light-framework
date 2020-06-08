<?php

namespace core;

class Core
{

    /**
     * @return string
     */
    public function start()
    {

        //Gets the uri without GET parameter
        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $routes = new Router($uri, $requestMethod);


        include '../routes/Routes.php';


    }

}