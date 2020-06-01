<?php


class HomeController extends \core\BaseController
{

    public function index(){
        echo $this->renderView('home.html');
    }

}