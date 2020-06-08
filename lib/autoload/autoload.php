<?php

//TODO: handle errors in files like that

function loadControllers($className){
    if (strpos($className, 'Controller')) {
        $className = str_replace('\\', '/', $className);
        $path = dirname(__DIR__, 2) . '/app/controller/';
        require_once $path . trim($className, '/') . '.php';
    }
}

function loadPolicies($className){
    if (strpos($className, 'Policy')) {
        $className = str_replace('\\', '/', $className);
        $path = dirname(__DIR__, 2) . '/app/policy/';
        require_once $path . $className . '.php';
    }
}

function loadModels($className){
    $path = dirname(__DIR__, 2).'/app/model/';
    require_once $path . $className .'.php';
}


spl_autoload_register('loadControllers');
spl_autoload_register('loadPolicies');
spl_autoload_register('loadModels');

