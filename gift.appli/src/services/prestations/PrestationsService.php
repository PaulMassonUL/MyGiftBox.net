<?php

namespace gift\app\services\prestations;

use Exception;
use gift\app\models\Categorie;
use gift\app\models\Prestation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PrestationsService
{
    public function getCategories(): array
    {
        $categories = Categorie::all();
        return $categories->toArray();
    }

    public function getCategorieById(int $id): array
    {
        try {
            $categorie = Categorie::findOrfail($id);
            return $categorie->toArray();
        } catch (ModelNotFoundException) {
            throw new CategorieNotFoundException();
        }
    }

    public function getPrestationById(string $id): array
    {
        try {
            $prestation = Prestation::findOrfail($id);
            return $prestation->toArray();
        } catch (ModelNotFoundException) {
            throw new PrestationNotFoundException();
        }
    }

    public function getPrestationsByCategorie(int $categ_id, string $tri = ""): array
    {
        try {
            $categorie = Categorie::where('id', $categ_id)
                ->with(['prestations' => function ($query) use ($tri) {
                    if (in_array($tri, ['asc', 'desc'])) $query->orderBy('tarif', $tri);
                }])
                ->firstOrFail();

            return $categorie->toArray();
        } catch (ModelNotFoundException) {
            throw new CategorieNotFoundException();
        }
    }

    /**
     * @throws Exception
     */
    public function createCategorie(array $data): void
    {
        $libelleFiltered = htmlspecialchars($data['libelle'], ENT_QUOTES, 'UTF-8');
        $descriptionFiltered = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');

        if ($data['libelle'] !== $libelleFiltered || $data['description'] !== $descriptionFiltered) {
            throw new Exception('Données suspectes fournies');
        }

        $categorie = new Categorie();
        $categorie->libelle = $libelleFiltered;
        $categorie->description = $descriptionFiltered;

        $categorieExist = Categorie::where('libelle', $libelleFiltered)->first();
        if($categorieExist){
            throw new Exception('La catégorie existe déjà');
        }
        $categorie->save();
    }
}