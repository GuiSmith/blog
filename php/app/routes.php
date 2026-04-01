<?php

$app->get('/', 'HomeController:index')->setName('home');

$app->group('/auth',function($app){
    $app->map(['GET','POST'],'/login','AuthController:login')->setName('auth.login');
    $app->map(['GET','POST'],'/registrar','AuthController:register')->setName('auth.register');
    $app->map(['GET','POST'],'/logout','AuthController:logout')->setName('auth.logout');
    $app->map(['GET','POST'],'/avatar','AuthController:avatar')->setName('auth.avatar');
});

$app->group('/post',function($app){
    $app->map(['GET','POST'],'/create','PostController:create')->setName('post.create');
    $app->map(['GET','POST'],'/update','PostController:update')->setName('post.update');
    $app->map(['DELETE'],'/delete','PostController:update')->setName('post.delete');
});