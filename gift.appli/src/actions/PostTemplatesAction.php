<?php

namespace gift\app\actions;

use gift\app\services\box\BoxService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class PostTemplatesAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        if (!isset($_SESSION['user'])) return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('signin'));

        if (!isset($args['template_id'])) throw new Exception('Missing template_id');

        $boxService = new BoxService();
        $boxId = $boxService->createBoxFromTemplate($args['template_id']);

        $_SESSION['current_box_id'] = $boxId;

        return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('boxesList'));
    }
}