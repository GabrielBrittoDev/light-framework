<?php

function loadControllers($className){
    if (strpos($className, 'Controller')) {
        $path = dirname(__DIR__, 2) . '/app/controller/';
        require_once $path . $className . '.php';
    }
}

function loadPolicies($className){
    if (strpos($className, 'Policy')) {
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

