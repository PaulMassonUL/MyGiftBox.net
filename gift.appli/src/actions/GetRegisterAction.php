<?php

namespace gift\app\actions;

use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class GetRegisterAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $disconnected = false;
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            $disconnected = true;
        }

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'GetRegisterView.twig', [
            'token' => CsrfService::generate(),
            'disconnected' => $disconnected
        ]);
    }
}