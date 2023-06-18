<?php

namespace gift\app\services\box;

use gift\app\models\Box;
use gift\app\models\BoxTemplate;
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

    public function editPrestationQuantity(string $id_presta, string $id_box, int $quantite): void
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
            throw new BoxNotFoundException("User not found during getBoxesByUser");
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
            throw new BoxNotFoundException();
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
            throw new BoxNotFoundException();
        }
    }

    public function validateBox(string $box_id)
    {
        try {
            $box = Box::with('users')->findOrFail($box_id);
            $box->statut = Box::STATUS_VALIDATED;
            $box->save();
        } catch (ModelNotFoundException) {
            throw new BoxNotFoundException();
        }
    }

    public function payBox(mixed $box_id): void
    {
        try {
            $box = Box::findOrFail($box_id);
            $box->statut = Box::STATUS_PAID;
            $box->save();
        } catch (ModelNotFoundException) {
            throw new BoxNotFoundException();
        }
    }

    public function deleteBox(string $box_id)
    {
        try {
            $box = Box::with('prestations', 'users')->findOrFail($box_id);

            $box->prestations()->detach();
            $box->users()->detach();
            $box->delete();
        } catch (ModelNotFoundException) {
            throw new BoxNotFoundException();
        }
    }

    public function getBoxByToken(string $box_token): array
    {
        try {
            $box = Box::with('prestations', 'users')->where('token', $box_token)->firstOrFail();
            return $box->toArray();
        } catch (ModelNotFoundException) {
            throw new BoxNotFoundException();
        }
    }

    public function deliverBox(string $box_id): void
    {
        try {
            $box = Box::findOrFail($box_id);
            $box->statut = Box::STATUS_DELIVERED;
            $box->save();
        } catch (ModelNotFoundException) {
            throw new BoxNotFoundException();
        }
    }

    public function openBox(string $box_id): void
    {
        try {
            $box = Box::findOrFail($box_id);
            $box->statut = Box::STATUS_OPENED;
            $box->save();
        } catch (ModelNotFoundException) {
            throw new BoxNotFoundException();
        }
    }

    public function getTemplateBoxes(): array
    {
        $boxes = BoxTemplate::with('prestations')->get();
        return $boxes->toArray();
    }

    public function createBoxFromTemplate(string $template_id): string
    {
        try {
            $template = BoxTemplate::with('prestations')->findOrFail($template_id);
            $box = new Box();
            $box->id = Uuid::uuid4()->toString();
            $box->token = bin2hex(random_bytes(32));
            $box->libelle = $template->libelle;
            $box->description = $template->description;
            $box->montant = $template->montant;
            $box->kdo = 0;
            $box->message_kdo = "";
            $box->statut = $box::STATUS_CREATED;
            if ($box->save()) {
                $box->users()->attach($_SESSION['user']);
                foreach ($template->prestations as $prestation) {
                    $box->prestations()->attach($prestation->id, ['quantite' => $prestation->pivot->quantite]);
                }
                return $box->id;
            } else {
                throw new \Exception('Failed to save box during box creation with template');
            }
        } catch (ModelNotFoundException) {
            throw new BoxNotFoundException();
        }
    }
}