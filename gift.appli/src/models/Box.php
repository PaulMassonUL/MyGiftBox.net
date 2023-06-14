<?php

namespace gift\app\models;

class Box extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'box';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public $keyType = 'string';
    public $incrementing = false;

    const STATUS_CREATED = 1;
    const STATUS_VALIDATED = 2;
    const STATUS_PAID = 3;
    const STATUS_DELIVERED = 4;
    const STATUS_OPENED = 5;

    public function prestations()
    {
        return $this->belongsToMany(Prestation::class, 'box2presta', 'box_id', 'presta_id')->withPivot('quantite');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'box2user', 'box_id', 'user_id');
    }
}