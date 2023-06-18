<?php

namespace gift\app\actions;

use gift\app\services\authentication\AuthenticationService;
use gift\app\services\authentication\RegistrationFailedException;
use gift\app\services\utils\CsrfException;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class PostRegisterAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $data = $rq->getParsedBody();

        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'GetRegisterView.twig', [
                'token' => CsrfService::generate(),
                'disconnected' => true,
            ]);
        }

        try {
            //Verification du token transmis par le formulaire
            $token = $data['csrf_token'] ?? null;
            try {
                CsrfService::check($token);
            } catch (CsrfException) {
                throw new CsrfException("Invalid CSRF token");
            }

            $auth = new AuthenticationService();
            $email = $auth->register($data);

            $_SESSION['user'] = $email;
            return $rs->withStatus(302)->withHeader('Location', RouteContext::fromRequest($rq)->getRouteParser()->urlFor('boxesList'));
        } catch (RegistrationFailedException $e) {
            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'GetRegisterView.twig', [
                'token' => CsrfService::generate(),
                'data' => $data,
                'error' => $e->getMessage()
            ]);
        }
    }
}