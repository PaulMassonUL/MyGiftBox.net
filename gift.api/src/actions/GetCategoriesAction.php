<?php

namespace gift\api\actions;

use gift\api\services\prestations\PrestationsService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCategoriesAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $presetations = new PrestationsService();
        $categories = $presetations->getCategories();

        $data = [
            'type' => 'collection',
            'count' => count($categories),
        ];
        foreach ($categories as $categorie) {
            $data['categories'][] = [
                'categorie' => [
                    'id' => $categorie['id'],
                    'libelle' => $categorie['libelle'],
                    'description' => $categorie['description'],
                ],
                'links' => [
                    'self' => [
                        'href' => '/categories/' . $categorie['id'] . '/',
                    ]
                ]
            ];
        }

        $rs->getBody()->write(json_encode($data));

        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}