<?php

namespace gift\app\models;

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'user';
    protected $primaryKey = 'email';
    public $timestamps = true;
    public $keyType = 'string';

    public function boxes() {
        return $this->belongsToMany(Box::class, 'box2user', 'user_id', 'box_id');
    }
}