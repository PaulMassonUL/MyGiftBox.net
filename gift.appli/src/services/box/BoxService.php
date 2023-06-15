<?php

namespace gift\app\services\box;

use gift\app\models\Box;
use gift\app\models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Uuid;

class BoxService
{
    public function createBox(array $data): string
    {
        if (isset($data['libelle']) && isset($data['description'])) {
            $box = new Box();
            $box->id = Uuid::uuid4()->toString();
            $box->token = bin2hex(random_bytes(32));
            $box->libelle = $data['libelle'];
            $box->description = $data['description'];
            $box->montant = 0.00;
            $box->kdo = isset($data['kdo']) ? 1 : 0;
            $box->message_kdo = $data['message_kdo'] ?? "";
            $box->statut = $box::STATUS_CREATED;
            if ($box->save()) {
                $box->users()->attach($_SESSION['user']);
                return $box->id;
            } else {
                throw new \Exception('Failed to save box during box creation');
            }
        }
        throw new \Exception('Missing libelle or description in box creation');
    }

    public function addPrestationToBox(string $id_presta, string $id_box, int $quantite): void
    {
        try {
            $box = Box::with('prestations')->findOrFail($id_box);
            $existingPrestation = $box->prestations()->where('presta_id', $id_presta)->first();

            if ($existingPrestation) {
                $pivotData = [
                    'quantite' => $existingPrestation->pivot->quantite + $quantite
                ];
                $box->prestations()->updateExistingPivot($id_presta, $pivotData);
            } else {
                $box->prestations()->attach($id_presta, ['quantite' => $quantite]);
            }

            $box->montant = $box->prestations()->get()->sum(function ($prestation) {
                return $prestation->tarif * $prestation->pivot->quantite;
            });
            $box->save();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException("Box not found during addPrestationToBox");
        }
    }

    public function editPrestationQuantity(string $id_presta, string $id_box, int $quantite) : void
    {
        try {
            $box = Box::with('prestations')->findOrFail($id_box);
            $box->prestations()->updateExistingPivot($id_presta, ['quantite' => $quantite]);
            $box->montant = $box->prestations()->get()->sum(function ($prestation) {
                return $prestation->tarif * $prestation->pivot->quantite;
            });
            $box->save();

        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException("Box not found during editQuantity");
        }
    }

    public function getBoxesByUser(string $user_id): array
    {
        try {
            $user = User::with('boxes')->findOrFail($user_id);
            return $user->boxes->toArray();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("User not found during getBoxesByUser");
        }

    }

    //get box by id
    /**
     * @throws BoxNotFoundException
     */
    public function getBoxById(string $id): array
    {
        try {
            $box = Box::with(['prestations' => function ($query) {
                $query->select('prestation.*', 'box2presta.quantite');
            }, 'prestations.categorie'])->findOrFail($id);

            $boxData = $box->toArray();
            $prestations = $boxData['prestations'];

            // Construire un nouveau tableau avec les informations nécessaires
            $result = $boxData;
            $result['prestations'] = [];

            foreach ($prestations as $prestation) {
                $prestationData = $prestation;
                $prestationData['quantite'] = $prestation['quantite'];
                $result['prestations'][] = $prestationData;
            }

            return $result;
        } catch (ModelNotFoundException) {
            throw new BoxNotFoundException();
        }
    }


    public function removePrestationFromBox(mixed $presta_id, mixed $box_id)
    {
        try {
            $box = Box::with('prestations')->findOrFail($box_id);
            $box->prestations()->detach($presta_id);
            $box->montant = $box->prestations()->get()->sum(function ($prestation) {
                return $prestation->tarif * $prestation->pivot->quantite;
            });
            $box->save();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException("Box not found during removePrestationFromBox");
        }
    }


    //fonction pour savoir si le coffret appartient à l'utilisateur

    public function isBoxOwner(string $box_id, string $user_id): bool
    {
        try {
            $box = Box::with('users')->findOrFail($box_id);
            $users = $box->users()->first();

            if ($users && $users->email === $user_id) {
                return true;
            } else {
                return false;
            }
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException("Box not found during isBoxOwner");
        }
    }

    //fonction connaitre le statut du coffret
    public function getBoxStatus(string $box_id): int
    {
        try {
            $box = Box::with('users')->findOrFail($box_id);
            $statut = $box->statut;
            return $statut;
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException("Box not found during getBoxStatus");
        }
    }

    public function validateBox(mixed $box_id): void
    {
        try {
            $box = Box::with('users')->findOrFail($box_id);
            $box->statut = Box::STATUS_VALIDATED;
            $box->save();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException("Box not found during validateBox");
        }
    }

    public function payBox(mixed $box_id): void
    {
        try {
            $box = Box::with('users')->findOrFail($box_id);
            $box->statut = Box::STATUS_PAID;
            $box->save();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException("Box not found during payBox");
        }
    }

}