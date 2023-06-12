<?php

declare(strict_types=1);

namespace gift\test\services\box;

use Faker\Factory;
use gift\app\models\Box;
use gift\app\models\Categorie;
use gift\app\models\Prestation;
use gift\app\services\box\BoxService;
use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase;

class BoxServiceTest extends TestCase
{
    private static array $boxes = [];
    private static array $prestations  = [];
    private static array $categories = [];
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $db = new DB();
        $db->addConnection(parse_ini_file(__DIR__ . '/../../../src/conf/gift.db.test.ini'));
        $db->setAsGlobal();
        $db->bootEloquent();
        $faker = Factory::create('fr_FR');
        $c1=Categorie::create([
            'libelle' => $faker->word(),
            'description' => $faker->paragraph(4)
        ]);
        self::$categories= [$c1];

        $p=Prestation::create([
            'id' => $faker->uuid(),
            'libelle' => $faker->word(),
            'description' => $faker->paragraph(3),
            'tarif' => $faker->randomFloat(2, 20, 200),
            'unite' => $faker->numberBetween(1, 3)
        ]);
        array_push(self::$prestations, $p);
        self::$prestations[0]->categorie()->associate($c1); self::$prestations[0]->save();
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::$boxes as $b) {
            $b->delete();
        }
    }

    //1. le service retourne un coffret avec un ID, dont l'état et le montant sont corrects
    //(état : CREATED et montant = 0), et dont le libellé et la description sont bien
    //ceux transmis,

    //2. le coffret existe dans la base, avec les même caractéristiques,

    public function testCreateBox(): void
    {
        $boxService = new BoxService();
        $boxId = $boxService->createBox([
            'libelle' => 'Coffret 1',
            'description' => 'desc 1',
        ]);

        $box = Box::find($boxId);

        $this->assertNotNull($boxId);
        $this->assertEquals(Box::STATUS_CREATED, $box->statut);
        $this->assertEquals(0.00, $box->montant);
        $this->assertEquals('Coffret 1', $box->libelle);
        $this->assertEquals('desc 1', $box->description);
        $boxFromDb = Box::find($box->id);
        $this->assertNotNull($boxFromDb);
        $box2 = $boxService->createBox([
            'libelle' => '',
            'description' => 'desc 1',
        ]);
        $this->assertNull($box2);
    }

    public function testAddPrestationToBox(): void
    {
        $boxService = new BoxService();
        $boxId = $boxService->createBox([
            'libelle' => 'Coffret 1',
            'description' => 'desc 1',
        ]);

        $boxService->addPrestationToBox(self::$prestations[0]->id, $boxId , 1);
        $boxFromDb = Box::find($boxId);
        $this->assertEquals(1, $boxFromDb->prestations()->count());
        $this->assertEquals(self::$prestations[0]->tarif, $boxFromDb->montant);
    }









}