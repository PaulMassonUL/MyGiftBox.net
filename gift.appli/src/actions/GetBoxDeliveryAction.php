<?php

namespace gift\app\actions;

use gift\app\models\Box;
use gift\app\services\box\BoxNotFoundException;
use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GetBoxDeliveryAction
{

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try{
            $boxService = new BoxService();

//            $box = $boxService->getBoxByToken($args['box_token']);

//            if ($box['statut'] != Box::STATUS_PAID) {
//                throw new HttpBadRequestException($rq, "Ce coffret n'existe pas.");
//            }

            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'GetBoxDeliveryView.twig', [
//                'box' => $box,
//                'token' => CsrfService::generate(),
            ]);
        } catch (BoxNotFoundException) {
            throw new HttpBadRequestException($rq, "Ce coffret n'existe pas.");
        }
    }

}