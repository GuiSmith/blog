<?php

namespace App\Controllers;

use App\Models\User;
use Respect\Validation\Validator as v;

class AuthController extends Controller {

    public function index($request,$response){
        // return $response->write($this->container['hello']);
        return $this->container->view->render($response,'index.twig');
    }

    public function login($request,$response){
        if($request->isGet()){
            return $this->container->view->render($response,'login.twig');
        }

        // Post
        if(!$this->container->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password'),)) {
            return $response->withRedirect($this->container->router->pathFor('auth.login'));
        }

        return $response->withRedirect($this->container->router->pathFor('home'));

    }

    public function register($request,$response){
        if($request->isGet()){
            return $this->container->view->render($response,'register.twig');
        }

        $ok_register = true;

        $validation = $this->container->validator->validate($request, [
            'name' => v::notEmpty()->alpha(' '),
            'email' => v::notEmpty()->noWhitespace()->email(),
            'password' => v::notEmpty()->noWhitespace()->length(8)
        ]);

        if($validation->failed()){
            // Usual validation ok
            $ok_register = false;
        }else{
            // Specific validations
            $user = User::where('email',$request->getParam('email'))->get();
            if($user->count() > 0){
                $this->container->flash->addMessage('warning', 'E-mail already registered. Choose another one!');
                $ok_register = false;
            }
        }

        if(!$ok_register){
            return $response->withRedirect($this->container->router->pathFor('auth.register'));
        }

        $data_hora_atual = new \Datetime(date('Y-m-d H:i:s'));
        $password_hash = password_hash($request->getParam('password'),PASSWORD_DEFAULT);

        User::create([
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => $password_hash,
            'confirmation_key' => 'asdasd',
            'confirmation_expires' => $data_hora_atual,
        ]);

        return $response->withRedirect($this->container->router->pathFor('auth.login'));
    }

    public function logout($request,$response){
        if($request->isGet()){
            $this->container->auth->logout();
            return $response->withRedirect($this->container->router->pathFor('home'));
        }
    }
};