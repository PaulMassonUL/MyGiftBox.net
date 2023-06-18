<?php

namespace gift\app\actions;

use Exception;
use gift\app\services\prestations\PrestationsService;
use gift\app\services\utils\CsrfException;
use gift\app\services\utils\CsrfService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class PostCategoriesCreateAction
{

    //L'action de traitement des données postées reçoit les données du formulaire et doit :
    //• récupérer ces données,
    //• vérifier la présence et la validité du token CSRF,
    //• les mettre dans la forme attendue par le service,
    //• appeler la méthode de création de catégorie dans le service,
    //• afficher la liste des catégories en retournant une redirection :
    //$rs->withStatus(302)->withHeader('Location', $url);

    /**
     * @throws CsrfException
     * @throws Exception
     */
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $data = $rq->getParsedBody();

        //Verification du token transmis par le formulaire
        $token = $data['csrf_token'] ?? '';
        try{
            CsrfService::check($token);
        }catch (CsrfException){
            throw new CsrfException("CSRF token is invalid");
        }

        //données du formulaire
        $categoryData = [
            'libelle' => $data['libelle'] ?? throw new Exception('Missing libelle'),
            'description' => $data['description'] ?? throw new Exception('Missing description'),
        ];

        $prestationSerice = new PrestationsService();
        $prestationSerice->createCategorie($categoryData);

        $url = RouteContext::fromRequest($rq)->getRouteParser()->urlFor('categoriesList');

        return $rs->withStatus(302)->withHeader('Location', $url);
    }

}