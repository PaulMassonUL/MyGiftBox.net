<?php

namespace gift\api\services\prestations;

use gift\api\models\Categorie;
use gift\api\models\Prestation;
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

    public function getPrestationsByCategorie(int $categ_id): array
    {
        try {
            Categorie::findOrfail($categ_id);
            $prestation = Prestation::where('cat_id', $categ_id)->get();
            return $prestation->toArray();
        } catch (ModelNotFoundException) {
            throw new CategorieNotFoundException();
        }
    }

}