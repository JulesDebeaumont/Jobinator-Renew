<?php

namespace App\DataFixtures;

use App\Factory\JobFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JobFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        JobFactory::createMany(50);
    }

    public function getDependencies(): array
    {
        return[
            TypeFixtures::class,
            CategoryFixtures::class,
            RecruterFixtures::class
        ];
    }
}
