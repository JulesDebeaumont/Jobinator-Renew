<?php

namespace App\DataFixtures;

use App\Factory\TypeFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $types = json_decode(file_get_contents(implode(
            DIRECTORY_SEPARATOR,
            [__DIR__, 
            'data', 
            'type.json'])), true);

        foreach ($types as $type)
        {
            TypeFactory::new()->create($type);
        }
    }
}
