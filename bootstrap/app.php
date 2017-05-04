<?php

use Respect\Validation\Validator;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'simplesvende2',
            'username' => 'root',
            'password' => 'gui0904',
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => ''
        ]
    ]
]);

// Twig Views
$container = $app->getContainer();

// Capsule from Illuminate DB (from Laravel db access layer)
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($container) use ($capsule){
    return $capsule;
};

// Respect Validation 
$container['validator'] = function($container){
    return new \App\Validation\Validator;
};

// Configuring views
$container['view'] = function($container){

    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,   
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

// HomeController in Container
$container['HomeController'] = function($container){
    return new \App\Controllers\HomeController($container);
};
// AuthController in Container
$container['AuthController'] = function($container){
    return new \App\Controllers\Auth\AuthController($container);
};

// Cross-Site Request Forgery Protection
$container['csrf'] = function($container){
    return new \Slim\Csrf\Guard;
};

// Authentication 
$container['auth'] = function($container){
    return new \App\Auth\Auth;
};

// Middleware for errors and persist old form data
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->get('csrf'));

// Custom validation rules
Validator::with('App\\Validation\\Rules\\');

// Routes
require __DIR__ . '/../app/routes.php';