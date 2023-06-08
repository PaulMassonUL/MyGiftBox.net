<?php

namespace gift\app\services\box;

use gift\app\models\Box;
use Ramsey\Uuid\Uuid;

class BoxService
{
    public function createBox(array $data): Box
    {
        $box = new Box();
        if (isset($data['libelle']) && isset($data['description'])) {
            $box->id = Uuid::uuid4()->toString();
            $box->token = bin2hex(random_bytes(32));
            $box->libelle = $data['libelle'];
            $box->description = $data['description'];
            $box->montant = 0.00;
            $box->kdo = isset($data['kdo']) ? 1 : 0;
            $box->message_kdo = $data['message_kdo'] ?? "";
            $box->statut = $box::STATUS_CREATED;
            $box->save();
        }
        return $box;
    }

    public function addPrestationToBox(string $id_presta, string $id_box): void
    {
        $box = Box::find($id_box);
        if (empty($box)) return;

        $box->prestations()->attach($id_presta, ['quantite' => 1]);
    }
}