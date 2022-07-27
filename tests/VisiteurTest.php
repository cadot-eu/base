<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Factory\CompteFactory;
use App\Service\base\TestHelper;
use App\Tests\basetests\Link;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Panther\Client;
use DOMNode;


/**
 * @method AppBrowser browser()
 */
class VisiteurTest extends PantherTestCase
{
    use HasBrowser;
    use Factories;



    public function testRecherche(): void
    {
        $this->Browser()
            ->visit('/?recherche=soleil')
            ->assertElementCount('#recherchebox ul li', 12);
    }
}
