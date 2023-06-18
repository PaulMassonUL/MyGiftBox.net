<?php

namespace gift\app\actions;

use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class GetTemplatesAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        if (!isset($_SESSION['user'])) return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('signin'));

        $boxService = new BoxService();

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'GetTemplatesView.twig', [
            'templates' => $boxService->getTemplateBoxes(),
            'token' => CsrfService::generate()
        ]);
    }
}