<?php

namespace App\DataFixtures;

use App\Entity\Recruter;
use App\Factory\RecruterFactory;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RecruterFixtures extends Fixture
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
        $recruter->setRoles(['ROLE_ADMIN']);
        $recruter->setName('Dider');
        $recruter->setSurname('Moulard');
        $birthday = new DateTime('1990-12-12');
        $recruter->setBirthday($birthday);
        $password = $this->encoder->encodePassword($recruter, '1234');
        $recruter->setPassword($password);
        $recruter->setCompany('Da Big Biz');
        $recruter->setMailCompany('bigbiz@outlook.fr');
        $recruter->setRoleInCompany('Web Dev Junior');

        $manager->persist($recruter);
        $manager->flush();

        $this->loadRandom(20);
    }

    /**
     * Génere des recruters avec des valeurs randoms
     * @param int $count Nombre de faux recruters à générer
     */
    private function loadRandom(int $count): void
    {
        RecruterFactory::createMany($count);
    }
}
