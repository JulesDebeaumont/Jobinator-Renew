<?php

namespace App\Factory;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Application>
 *
 * @method static Application|Proxy createOne(array $attributes = [])
 * @method static Application[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Application|Proxy find(object|array|mixed $criteria)
 * @method static Application|Proxy findOrCreate(array $attributes)
 * @method static Application|Proxy first(string $sortedField = 'id')
 * @method static Application|Proxy last(string $sortedField = 'id')
 * @method static Application|Proxy random(array $attributes = [])
 * @method static Application|Proxy randomOrCreate(array $attributes = [])
 * @method static Application[]|Proxy[] all()
 * @method static Application[]|Proxy[] findBy(array $attributes)
 * @method static Application[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Application[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ApplicationRepository|RepositoryProxy repository()
 * @method Application|Proxy create(array|callable $attributes = [])
 */
final class ApplicationFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'description' => self::faker()->optional()->realText(300),
            'job' => JobFactory::random(),
            'candidat' => CandidatFactory::random()
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Application $application) {})
        ;
    }

    protected static function getClass(): string
    {
        return Application::class;
    }
}
