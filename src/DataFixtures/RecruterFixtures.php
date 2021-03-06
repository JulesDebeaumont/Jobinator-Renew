<?php

namespace App\DataFixtures;

use App\Entity\Recruter;
use App\Factory\RecruterFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class RecruterFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $recruter = new Recruter();
        $recruter->setEmail('another@hotmail.fr');
        $recruter->setRoles(['ROLE_USER', 'ROLE_RECRUTER']);
        $password = $this->encoder->encodePassword($recruter, 'abcd');
        $recruter->setPassword($password);
        $recruter->setCompany('Da Big Biz');
        $manager->persist($recruter);

        $anotherRecruter = new Recruter();
        $anotherRecruter->setEmail('anotherRecruter@hotmail.fr');
        $anotherRecruter->setRoles(['ROLE_USER', 'ROLE_RECRUTER']);
        $anotherPassword = $this->encoder->encodePassword($recruter, 'abcde');
        $anotherRecruter->setPassword($anotherPassword);
        $anotherRecruter->setCompany('Nimpize');
        $manager->persist($anotherRecruter);

        $manager->flush();

        $this->loadRandom(10);
    }

    /**
     * Génere des recruters avec des valeurs randoms
     * @param int $count Nombre de faux recruters à générer
     */
    private function loadRandom(int $count): void
    {
        RecruterFactory::createMany($count);
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
