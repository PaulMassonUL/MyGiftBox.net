<?php

namespace gift\app\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GetPaiementAction extends Action
{

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'GetPaiementView.twig', [
            'box_id' => $args['box_id'],
        ]);

    }
}