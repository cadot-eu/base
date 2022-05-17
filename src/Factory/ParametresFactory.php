<?php

namespace App\Factory;

use App\Entity\Parametres;
use App\Repository\ParametresRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Parametres>
 *
 * @method static Parametres|Proxy createOne(array $attributes = [])
 * @method static Parametres[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Parametres|Proxy find(object|array|mixed $criteria)
 * @method static Parametres|Proxy findOrCreate(array $attributes)
 * @method static Parametres|Proxy first(string $sortedField = 'id')
 * @method static Parametres|Proxy last(string $sortedField = 'id')
 * @method static Parametres|Proxy random(array $attributes = [])
 * @method static Parametres|Proxy randomOrCreate(array $attributes = [])
 * @method static Parametres[]|Proxy[] all()
 * @method static Parametres[]|Proxy[] findBy(array $attributes)
 * @method static Parametres[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Parametres[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ParametresRepository|RepositoryProxy repository()
 * @method Parametres|Proxy create(array|callable $attributes = [])
 */
final class ParametresFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'nom' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Parametres $parametres): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Parametres::class;
    }
}
