<?php

namespace gift\app\actions;

use gift\app\services\prestations\CategorieNotFoundException;
use gift\app\services\prestations\PrestationsService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GetPrestationsAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {
            $prestationsService = new PrestationsService();

            $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'GetPrestationsView.twig', [
                'prestationsRoute' => $routeParser->urlFor('prestationsList', ['id' => $args['id']]),
                'categorie' => $args['id'],
                'prestations' => $prestationsService->getPrestationsByCategorie($args['id'])
            ]);
        } catch (CategorieNotFoundException $e) {
            throw new HttpBadRequestException($rq, "Impossible de trouver la catégorie " . $args['id']);
        }
    }
}