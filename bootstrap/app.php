<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true,
	]
]);

// Twig Views
$container = $app->getContainer();

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

// Routes
require __DIR__ . '/../app/routes.php';