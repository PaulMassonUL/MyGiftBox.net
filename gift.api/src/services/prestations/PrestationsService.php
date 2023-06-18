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

    public function getPrestations(): array
    {
        $prestations = Prestation::all();
        return $prestations->toArray();
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