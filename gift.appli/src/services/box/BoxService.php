<?php

namespace gift\app\services\box;

use gift\app\models\Box;
use Ramsey\Uuid\Uuid;

class BoxService
{
    public function createBox(array $data)
    {
        if (empty($data['libelle']) || empty($data['description'])) return;

        $box = new Box();
        $box->id = Uuid::uuid4()->toString();
        $box->token = bin2hex(random_bytes(32));
        $box->libelle = $data['libelle'];
        $box->description = $data['description'];
        $box->montant = 0.00;
        $box->kdo = $data['kdo'] ?? 0;
        $box->message_kdo = $data['message_kdo'] ?? '';
        $box->statut = $box::STATUS_CREATED;
        $box->save();
    }
}