<?php

use gift\app\actions\GetBoxesNewAction;
use gift\app\actions\GetCategorieAction;
use gift\app\actions\GetCategoriesAction;
use gift\app\actions\GetPrestationAction;
use gift\app\actions\GetPrestationsAction;
use gift\app\actions\PostBoxesNewAction;

require_once __DIR__ . '/../vendor/autoload.php';

return function (\Slim\App $app): void {

    $app->get('/categories[/]', GetCategoriesAction::class)->setName('categoriesList');

    $app->get('/categories/{id}[/]', GetCategorieAction::class)->setName('categorie');

    $app->get('/categories/{id}/prestations[/]', GetPrestationsAction::class)->setName('prestationsList');

    $app->get('/categories/{cat_id}/prestations/{presta_id}[/]', GetPrestationAction::class)->setName('prestation');

    $app->get('/boxes/new[/]', GetBoxesNewAction::class)->setName('boxesNew');

    $app->post('/boxes/new[/]', PostBoxesNewAction::class)->setName('boxesNewCreated');
};