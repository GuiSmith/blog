<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends Controller {

    public function index($request,$response){
        // return $response->write($this->container['hello']);
        return $this->container->view->render($response,'index.twig');
    }

    public function login($request,$response){
        return $this->container->view->render($response,'login.twig');
    }

    public function register($request,$response){
        if($request->isGet()){
            return $this->container->view->render($response,'register.twig');
        }

        $data_hora_atual = new \Datetime(date('Y-m-d H:i:s'));

        User::create([
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => $request->getParam('password'),
            'confirmation_key' => 'asdasd',
            'confirmation_expires' => $data_hora_atual,
        ]);

        return $response->withRedirect($this->container->router->pathFor('auth.login'));
    }
};