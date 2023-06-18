<?php

namespace gift\app\models;

class BoxTemplate extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'boxtemplate';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function prestations()
    {
        return $this->belongsToMany(Prestation::class, 'template2presta', 'template_id', 'presta_id')->withPivot('quantite');
    }
}