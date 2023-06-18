<?php

namespace gift\app\actions;

use gift\app\models\Box;
use gift\app\services\box\BoxNotFoundException;
use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfException;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class PostBoxDeliveryAction
{

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {

            $data = $rq->getParsedBody();

            //Verification du token transmis par le formulaire
            $token = $data['csrf_token'] ?? null;
//            try {
//                CsrfService::check($token);
//            } catch (CsrfException) {
//                throw new CsrfException("Invalid CSRF token");
//            }

            $boxService = new BoxService();
            $box = $boxService->getBoxByToken($args['box_token']);

//            if ($box['statut'] != Box::STATUS_DELIVERED) {
//                throw new HttpBadRequestException($rq, "Ce coffret n'existe pas.");
//            }

            $boxService->openBox($box['id']);

            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'PostBoxDeliveryView.twig', [
                'box' => $box,
                'user' => $box['users'][0],
            ]);
        } catch (BoxNotFoundException) {
            throw new HttpBadRequestException($rq, "Ce coffret n'existe pas.");
        }
    }

}