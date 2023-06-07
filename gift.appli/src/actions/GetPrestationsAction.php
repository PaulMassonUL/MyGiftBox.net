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

            $tri = $rq->getQueryParams()['tri'] ?? "";

            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'GetPrestationsView.twig', [
                'categorie' => $prestationsService->getPrestationsByCategorie($args['id'], $tri),
            ]);
        } catch (CategorieNotFoundException $e) {
            throw new HttpBadRequestException($rq, "Impossible de trouver la catégorie " . $args['id'] . ". " . $e->getMessage());
        }
    }
}