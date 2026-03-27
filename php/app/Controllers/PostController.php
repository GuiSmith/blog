<?php

namespace App\Controllers;

class PostController extends Controller {

    public function create($request,$response){
        if($request->isGet()){
            return $this->container->view->render($response,'post/create.twig');
        }
    }
};