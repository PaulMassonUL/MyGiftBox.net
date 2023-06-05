<?php

use gift\api\services\utils\Eloquent;

$app = \Slim\Factory\AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);

Eloquent::init(__DIR__.'/gift.db.conf.ini');

return $app;