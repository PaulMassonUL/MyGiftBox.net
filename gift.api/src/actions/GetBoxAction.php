<?php

namespace gift\api\actions;

use gift\api\services\box\BoxService;
use gift\api\services\prestations\BoxNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetBoxAction extends Action
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        try {
            $boxService = new BoxService();
            $box = $boxService->getBoxById($args['ID']);

            $data = [
                'type' => 'resource',
                'box' => [
                    'id' => $box['id'],
                    'libelle' => $box['libelle'],
                    'description' => $box['description'],
                    'message_kdo' => $box['message_kdo'],
                    'statut' => $box['statut'],
                ]
            ];
            foreach ($box['prestations'] as $prestation) {
                $data['box']['prestations'][] = [
                    'libelle' => $prestation['libelle'],
                    'description' => $prestation['description'],
                    'contenu' => [
                        'box_id' => $prestation['pivot']['box_id'],
                        'presta_id' => $prestation['pivot']['presta_id'],
                        'quantite' => $prestation['pivot']['quantite'],
                    ]
                ];
            }

            $rs->getBody()->write(json_encode($data));
            return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (BoxNotFoundException) {
            return $rs->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }
}