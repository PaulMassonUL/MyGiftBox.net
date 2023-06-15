<?php

namespace gift\app\actions;

use gift\app\services\box\BoxNotFoundException;
use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfException;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
class GetValidateBoxAction extends Action
{

        public function __invoke(Request $rq, Response $rs, array $args): Response
        {
            try{
                $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

                $boxService = new BoxService();

                //if(!$boxService->isBoxOwner($args['box_id'], $_SESSION['user'])){
                //    throw new HttpBadRequestException($rq,"Vous n'êtes pas autorisé à accéder à ce coffret");
                //}

                $box = $boxService->getBoxById($args['box_id']);

                $boxService->validateBox($args['box_id']);

                return $rs->withStatus(302)->withHeader('Location', $routeParser->urlFor('box', ['box_id' => $args['box_id']]));

            } catch (BoxNotFoundException $e) {
                throw new HttpBadRequestException($rq, "Impossible de trouver le coffret " . $args['box_id']);
            }
        }
}