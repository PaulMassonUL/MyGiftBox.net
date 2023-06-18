<?php

namespace gift\api\actions;

use gift\api\services\prestations\PrestationsService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetPrestationsAction
{

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $prestations = new PrestationsService();
        $prestations = $prestations->getPrestations();

        $data = [
            'type' => 'collection',
            'count' => count($prestations),
        ];
        foreach ($prestations as $presta) {
            $data['prestations'][] = [
                'presta' => [
                    'id' => $presta['id'],
                    'libelle' => $presta['libelle'],
                    'description' => $presta['description'],
                    'tarif' => $presta['tarif'],
                ],
                'links' => [
                    'self' => [
                        'href' => '/prestations/' . $presta['id'] . '/',
                    ]
                ]
            ];
        }

        $rs->getBody()->write(json_encode($data));

        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}