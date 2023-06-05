<?php

use gift\api\actions\GetBoxAction;
use gift\api\actions\GetCategoriesAction;

require_once __DIR__ . '/../vendor/autoload.php';

return function (\Slim\App $app): void {

    $app->get('/api/categories', GetCategoriesAction::class)->setName('categories');

    $app->get('/api/boxes/{ID}[/]', GetBoxAction::class)->setName('box');

};