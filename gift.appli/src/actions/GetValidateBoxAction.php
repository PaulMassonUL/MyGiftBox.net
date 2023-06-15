<?php

namespace gift\app\actions;

use gift\app\services\box\BoxNotFoundException;
use gift\app\services\box\BoxService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;

class GetValidateBoxAction extends Action
{

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {
            $boxService = new BoxService();

            if (!$boxService->isBoxOwner($args['box_id'], $_SESSION['user'])) {
                throw new HttpBadRequestException($rq, "Vous n'êtes pas autorisé à accéder à ce coffret");
            }

            $box = $boxService->getBoxById($args['box_id']);
            if (count($box['prestations']) == 0) {
                throw new HttpBadRequestException($rq, "Vous ne pouvez pas valider un coffret vide");
            }

            $boxService->validateBox($args['box_id']);

            return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('box', ['box_id' => $args['box_id']]));

        } catch (BoxNotFoundException $e) {
            throw new HttpBadRequestException($rq, "Impossible de trouver le coffret " . $args['box_id']);
        }
    }
}