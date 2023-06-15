<?php

namespace gift\app\actions;

use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfException;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;

class PostBoxAction extends Action
{

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $data = $rq->getParsedBody();

        $boxService = new BoxService();
        if (!$boxService->isBoxOwner($args['box_id'], $_SESSION['user'])) {
            throw new HttpBadRequestException($rq, "Vous n'êtes pas autorisé à accéder à ce coffret");
        }

        //Verification du token transmis par le formulaire
        $token = $data['csrf_token'] ?? null;

        try {
            CsrfService::check($token);
        } catch (CsrfException) {
            throw new CsrfException("Invalid CSRF token");
        }

        if (isset($data['validate'])) {
            $boxService->validateBox($args['box_id']);
        }

        if (isset($data['presta_id']) && isset($args['box_id'])) {
            if (isset($data['maj'])) {
                $qty = ($data['qty'] && is_numeric($data['qty']) && $data['qty'] > 0) ? $data['qty'] : 1;
                $boxService->editPrestationQuantity($data['presta_id'], $args['box_id'], $qty);
            } else if (isset($data['del'])) {
                $boxService->removePrestationFromBox($data['presta_id'], $args['box_id']);
            }
        }

        if (isset($data['addPresta'])) {
            $_SESSION['current_box_id'] = $args['box_id'];
            return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('categoriesList'));
        } elseif (isset($data['delBox'])) {
            $boxService->deleteBox($args['box_id']);
            return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('boxesList'));
        }

        return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('box', ['box_id' => $args['box_id']]));
    }

}