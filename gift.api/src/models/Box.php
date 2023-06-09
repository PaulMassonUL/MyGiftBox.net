<?php

namespace gift\api\models;

class Box extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'box';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public $keyType = 'string';

    const STATUS_CREATED = 1;

    public function prestations() {
        return $this->belongsToMany(Prestation::class, 'box2presta', 'box_id', 'presta_id')->withPivot('quantite');
    }
}