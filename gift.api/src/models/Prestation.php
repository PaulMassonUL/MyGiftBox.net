<?php

namespace gift\api\models;

class Prestation extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'prestation';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $keyType = 'string';

    public function categorie() {
        return $this->belongsTo(Categorie::class, 'cat_id');
    }
    
}