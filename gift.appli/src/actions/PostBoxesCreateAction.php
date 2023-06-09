<?php

namespace gift\app\actions;

use Exception;
use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfException;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class PostBoxesCreateAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $data = $rq->getParsedBody();

        if (!isset($_SESSION['user'])) return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('signin'));

        //Verification du token transmis par le formulaire
        $token = $data['csrf_token'] ?? '';
        try{
            CsrfService::check($token);
        }catch (CsrfException){
            throw new CsrfException("CSRF token is invalid");
        }

        if (!isset($data['libelle'])) throw new Exception('Missing libelle');
        if (!isset($data['description'])) throw new Exception('Missing description');

        $boxService = new BoxService();
        $boxId = $boxService->createBox($data);

        $_SESSION['current_box_id'] = $boxId;

        return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('boxesList'));
    }
}