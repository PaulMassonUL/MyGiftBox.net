<?php

namespace gift\api\models;

class Categorie extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'categorie';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function prestations() {
        return $this->hasMany(Prestation::class, 'cat_id');
    }
    
}