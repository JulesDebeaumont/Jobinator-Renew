<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $$types = json_decode(file_get_contents(implode(
            DIRECTORY_SEPARATOR,
            [
                __DIR__,
                'data',
                'category.json'
            ]
        )), true);

        foreach ($types as $type) {
            $newType = new Category;
            $newType->setName($type['name']);

            $manager->persist($newType);
        }

        $manager->flush();
    }
}
