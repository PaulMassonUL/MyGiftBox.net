<?php

namespace gift\app\actions;

use Exception;
use gift\app\models\Box;
use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfException;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostPaiementAction extends Action
{

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {

        //redirection après avoir payé vers PostPaiementAction.twig

        //Verification du token transmis par le formulaire
        $token = $data['csrf_token'] ?? '';
        try {
            CsrfService::check($token);
        } catch (CsrfException) {
            throw new CsrfException("Invalid CSRF token");
        }

        $boxService = new BoxService();
        $box = $boxService->getBoxById($args['box_id']);

        //user est bien propriétaire et box est validée
        if (($box['statut'] == Box::STATUS_VALIDATED) && ($boxService->isBoxOwner($args['box_id'], $_SESSION['user']))){
            $boxService->payBox($args['box_id']);
            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'PostPaiementAction.twig', [
                'box_id' => $args['box_id'],
            ]);
        } else {
            throw new Exception("La box n'est pas validée et/ou ne vous appartient pas");
        }


    }
}