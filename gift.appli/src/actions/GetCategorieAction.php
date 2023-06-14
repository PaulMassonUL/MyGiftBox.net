<?php

namespace gift\app\actions;

use gift\app\services\prestations\CategorieNotFoundException;
use gift\app\services\prestations\PrestationsService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GetCategorieAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

            $prestationsService = new PrestationsService();
            $categorie = $prestationsService->getCategorieById($args['id']);
        } catch (CategorieNotFoundException) {
            throw new HttpBadRequestException($rq, "Impossible de trouver la catégorie " . $args['id']);
        }

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'GetCategorieView.twig', [
            'categoriesRoute' => $routeParser->urlFor('categoriesList'),
            'prestationsRoute' => $routeParser->urlFor('prestationsList', ['id' => $args['id']]),
            'categorie' => $categorie
        ]);
    }
}