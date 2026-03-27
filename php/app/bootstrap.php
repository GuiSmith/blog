<?php

session_start();
date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$app = new Slim\App(
    [
        "settings" => [
            'displayErrorDetails' => true,
            'db' => [
                'driver' => $_ENV['DB_DRIVER'],
                'host' => $_ENV['DB_HOST'],
                'database' => $_ENV['MYSQL_DATABASE'],
                'username' => $_ENV['MYSQL_USER'],
                'password' => $_ENV['MYSQL_PASSWORD'],
                'port' => $_ENV['DB_PORT'],
                'collation' => 'utf8_unicode_ci',
            ]
        ]
    ]
);
$container = $app->getContainer();

$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['validator'] = function($container){
    return new App\Utils\Validator;
};

$container['flash'] = function($container){
    return new Slim\Flash\Messages;
};

$container['auth'] = function($container){
    return new App\Auth\Auth($container);
};

$container['view'] = function($container){
    $view = new Slim\Views\Twig(__DIR__ . "/../resources/views",[
        'cache' => false,
    ]);

    $view->addExtension(new Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    $view->getEnvironment()->addGlobal('auth', [
       'check' => $container->auth->check(),
       'user' => $container->auth->user(), 
    ]);

    return $view;

};

$container['HomeController'] = function ($container){
    return new App\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container){
    return new App\Controllers\AuthController($container);
};

$container['PostController'] = function ($container){
    return new App\Controllers\PostController($container);
};

$app->add(new App\Middleware\MiddlewareErrors($container));

require __DIR__ . '/routes.php';
