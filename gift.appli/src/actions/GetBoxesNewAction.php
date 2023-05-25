<?php

namespace gift\app\actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class GetBoxesNewAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'GetBoxesNewView.twig');
    }
}