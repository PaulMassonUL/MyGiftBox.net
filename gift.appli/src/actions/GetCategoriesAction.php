<?php

namespace gift\app\actions;

use gift\app\services\prestations\PrestationsService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GetCategoriesAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $prestationsService = new PrestationsService();

        $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'GetCategoriesView.twig', [
            'categoriesRoute' => $routeParser->urlFor('categoriesList'),
            'categories' => $prestationsService->getCategories()
        ]);
    }
}