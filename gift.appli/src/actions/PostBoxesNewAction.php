<?php

namespace gift\app\actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class PostBoxesNewAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $data = $rq->getParsedBody();

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'PostBoxesNewView.twig', [
            'data' => $data,
        ]);
    }
}