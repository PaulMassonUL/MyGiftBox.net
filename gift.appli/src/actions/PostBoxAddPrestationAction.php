<?php

namespace gift\app\actions;

use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfException;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class PostBoxAddPrestationAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $data = $rq->getParsedBody();

        //Verification du token transmis par le formulaire
        $token = $data['csrf_token'] ?? null;

        try {
            CsrfService::check($token);
        } catch (CsrfException) {
            throw new CsrfException("Invalid CSRF token");
        }

        if (isset($data['presta_id']) && isset($_SESSION['current_box_id'])) {
            $boxService = new BoxService();
            $qty = ($data['qty'] && is_numeric($data['qty']) && $data['qty'] > 0) ? $data['qty'] : 1;
            $boxService->addPrestationToBox($data['presta_id'], $_SESSION['current_box_id'], $qty);
        }

        $url = RouteContext::fromRequest($rq)->getRouteParser()->urlFor('prestationsList', ['id' => $args['cat_id']]);

        return $rs->withStatus(302)->withHeader('Location', $url);
    }
}