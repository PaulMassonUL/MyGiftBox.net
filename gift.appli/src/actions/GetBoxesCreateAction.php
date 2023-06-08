<?php

namespace gift\app\actions;

use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class GetBoxesCreateAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $token = CsrfService::generate();

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'GetBoxesCreateView.twig',
            ['token' => $token]
        );
    }
}