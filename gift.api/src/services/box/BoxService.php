<?php

namespace gift\api\services\box;

use gift\api\models\Box;
use gift\api\services\prestations\BoxNotFoundException;

class BoxService
{
    public function getBoxById(string $id): array
    {
        try {
            $box = Box::where('id', $id)->with('prestations')->firstOrFail();
            return $box->toArray();
        } catch (BoxNotFoundException) {
            throw new BoxNotFoundException();
        }
    }
}