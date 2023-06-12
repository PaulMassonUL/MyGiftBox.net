<?php

namespace gift\app\models;

class Prestation extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'prestation';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'libelle',
        'description',
        'tarif',
        'unite'
    ];

    public function categorie() {
        return $this->belongsTo(Categorie::class, 'cat_id');
    }
    
}