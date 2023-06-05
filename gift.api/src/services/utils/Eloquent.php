<?php

namespace gift\api\services\utils;

class Eloquent
{
    public static function init(String $config): void
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection(parse_ini_file($config));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}