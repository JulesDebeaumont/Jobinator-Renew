<?php

namespace App\Factory;

use App\Entity\Candidat;
use App\Repository\CandidatRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @extends ModelFactory<Candidat>
 *
 * @method static Candidat|Proxy createOne(array $attributes = [])
 * @method static Candidat[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Candidat|Proxy find(object|array|mixed $criteria)
 * @method static Candidat|Proxy findOrCreate(array $attributes)
 * @method static Candidat|Proxy first(string $sortedField = 'id')
 * @method static Candidat|Proxy last(string $sortedField = 'id')
 * @method static Candidat|Proxy random(array $attributes = [])
 * @method static Candidat|Proxy randomOrCreate(array $attributes = [])
 * @method static Candidat[]|Proxy[] all()
 * @method static Candidat[]|Proxy[] findBy(array $attributes)
 * @method static Candidat[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Candidat[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CandidatRepository|RepositoryProxy repository()
 * @method Candidat|Proxy create(array|callable $attributes = [])
 */
final class CandidatFactory extends ModelFactory
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'password' => self::faker()->password(),
            'name' => self::faker()->firstName(),
            'surname' => self::faker()->lastName(),
            'birthday' => self::faker()->dateTime(),
            'phone' => self::faker()->phoneNumber(),
            'country' => self::faker()->country(),
            'city' => self::faker()->city(),
            'departement' => self::faker()->randomNumber(2),
            'roles' => ['ROLE_USER' ,'ROLE_CANDIDAT']
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function(Candidat $candidat) {
                $candidat->setPassword($this->passwordEncoder->encodePassword($candidat, $candidat->getPassword()));
            })
        ;
    }

    protected static function getClass(): string
    {
        return Candidat::class;
    }
}
