<?php

namespace gift\app\actions;

use gift\app\services\box\BoxService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GetBoxesAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        if (!isset($_SESSION['user'])) return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('signin'));

        $boxService = new BoxService();
        $boxes = $boxService->getBoxesByUser($_SESSION['user']);

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'GetBoxesView.twig',
            ['boxes' => $boxes]
        );
    }
}