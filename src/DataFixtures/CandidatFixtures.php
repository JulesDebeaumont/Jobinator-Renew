<?php

namespace App\DataFixtures;

use App\Entity\Candidat;
use App\Factory\CandidatFactory;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CandidatFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $candidat = new Candidat();
        $candidat->setEmail('random@yahoo.fr');
        $candidat->setRoles(['ROLE_CANDIDAT']);
        $candidat->setName('Didier');
        $candidat->setSurname('Moulard');
        $birthday = new DateTime('1995-01-03');
        $candidat->setBirthday($birthday);
        $password = $this->encoder->encodePassword($candidat, '1234');
        $candidat->setPassword($password);

        $manager->persist($candidat);
        $manager->flush();

        $this->loadRandom(20);
    }

    /**
     * Génere des candidats avec des valeurs randoms
     * @param int $count Nombre de faux candidat à générer
     */
    private function loadRandom(int $count): void
    {
        CandidatFactory::createMany($count);
    }
}
