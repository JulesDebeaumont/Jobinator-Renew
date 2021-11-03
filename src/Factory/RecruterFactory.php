<?php

namespace App\Factory;

use App\Entity\Recruter;
use App\Repository\RecruterRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @extends ModelFactory<Recruter>
 *
 * @method static Recruter|Proxy createOne(array $attributes = [])
 * @method static Recruter[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Recruter|Proxy find(object|array|mixed $criteria)
 * @method static Recruter|Proxy findOrCreate(array $attributes)
 * @method static Recruter|Proxy first(string $sortedField = 'id')
 * @method static Recruter|Proxy last(string $sortedField = 'id')
 * @method static Recruter|Proxy random(array $attributes = [])
 * @method static Recruter|Proxy randomOrCreate(array $attributes = [])
 * @method static Recruter[]|Proxy[] all()
 * @method static Recruter[]|Proxy[] findBy(array $attributes)
 * @method static Recruter[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Recruter[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static RecruterRepository|RepositoryProxy repository()
 * @method Recruter|Proxy create(array|callable $attributes = [])
 */
final class RecruterFactory extends ModelFactory
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
            'password' => self::faker()->text(),
            'company' => self::faker()->company(),
            'roles' => ['ROLE_USER' ,'ROLE_RECRUTER']
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function(Recruter $recruter) {
                $recruter->setPassword($this->passwordEncoder->encodePassword($recruter, $recruter->getPassword()));
            })
        ;
    }

    protected static function getClass(): string
    {
        return Recruter::class;
    }
}
