<?php

namespace App\Controllers;

class HomeController extends Controller {

    public function index($request,$response){
        // return $response->write($this->container['hello']);
        return $this->container->view->render($response,'index.twig');
    }
};