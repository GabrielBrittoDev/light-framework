<?php


class ErrorController extends \core\BaseController
{
    public function handle(int $error, $args = array()){
        $errorList = [404 => 'not_found.html', 500 => 'server_error.html'];
        echo $this->renderView('errors/'. ($errorList[$error] ?? 500));
    }
}