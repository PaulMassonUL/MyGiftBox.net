<?php

use gift\app\services\utils\Eloquent;

$app = \Slim\Factory\AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);

// Create Twig
$twig = \Slim\Views\Twig::create(__DIR__.'/../views', [
    'cache' => __DIR__.'/../views/cache',
    'auto_reload' => true]
);
$app->add(\Slim\Views\TwigMiddleware::create($app, $twig));

Eloquent::init(__DIR__.'/gift.db.conf.ini');

return $app;