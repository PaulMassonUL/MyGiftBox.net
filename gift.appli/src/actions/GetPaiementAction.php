<?php

namespace gift\app\actions;

use Exception;
use gift\app\models\Box;
use gift\app\services\box\BoxService;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GetPaiementAction extends Action
{

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

        $boxService = new BoxService();
        $box = $boxService->getBoxById($args['box_id']);

        //user est bien propriétaire et box est validée
        if (($box['statut'] == Box::STATUS_VALIDATED) && ($boxService->isBoxOwner($args['box_id'], $_SESSION['user']))) {
            $view = Twig::fromRequest($rq);
            return $view->render($rs, 'GetPaiementView.twig', [
                'box_id' => $args['box_id'],
                'token' => CsrfService::generate(),
                'paiementRoute' => $routeParser->urlFor('boxPaid', ['box_id' => $args['box_id']]),
            ]);
        } else {
            throw new Exception("La box n'est pas validée et/ou ne vous appartient pas");
        }
    }
}