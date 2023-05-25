<?php

namespace gift\app\actions;

use gift\app\services\prestations\PrestationNotFoundException;
use gift\app\services\prestations\PrestationsService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GetPrestationAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

            $prestationService = new PrestationsService();
            $prestation = $prestationService->getPrestationById($args['presta_id']);

            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'GetPrestationView.twig', [
                'prestationsRoute' => $routeParser->urlFor('prestationsList', ['id' => $args['cat_id']]),
                'prestation' => $prestation,
                'image' => "../../../../img/" . trim($prestation['img'])
            ]);
        } catch (PrestationNotFoundException $e) {
            throw new HttpBadRequestException($rq, "Impossible de trouver la prestation " . $args['presta_id']);
        }

    }
}