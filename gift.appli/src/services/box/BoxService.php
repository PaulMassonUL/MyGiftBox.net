<?php

namespace gift\app\services\box;

use gift\app\models\Box;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class BoxService
{
    public function createBox(array $data): string
    {
        if (isset($data['libelle']) && isset($data['description'])) {
            $box = new Box();
            $uuid = Uuid::uuid4()->toString();
            $box->id = $uuid;
            $box->token = bin2hex(random_bytes(32));
            $box->libelle = $data['libelle'];
            $box->description = $data['description'];
            $box->montant = 0.00;
            $box->kdo = isset($data['kdo']) ? 1 : 0;
            $box->message_kdo = $data['message_kdo'] ?? "";
            $box->statut = $box::STATUS_CREATED;
            if ($box->save()) return $uuid;
        }
        throw new \Exception('Missing libelle or description in box creation');
    }

    public function addPrestationToBox(string $id_presta, string $id_box, int $quantite): void
    {
        try {
            $box = Box::findOrFail($id_box);
            $existingPrestation = $box->prestations()->where('presta_id', $id_presta)->first();

            if ($existingPrestation) {
                $pivotData = [
                    'quantite' => $existingPrestation->pivot->quantite + $quantite
                ];
                $box->prestations()->updateExistingPivot($id_presta, $pivotData);
            } else {
                $box->prestations()->attach($id_presta, ['quantite' => $quantite]);
            }
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException("Box not found during addPrestationToBox");
        }

    }
}