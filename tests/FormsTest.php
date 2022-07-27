<?php

namespace App\Tests;

use DOMNode;
use DateTime;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Zenstruck\Browser\Test\HasBrowser;
use App\Entity\base\TimeTrait;
use App\Tests\basetests\Form;
use App\Tests\basetests\Link;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/** @var \Zenstruck\Browser\KernelBrowser $Browser **/
class FormsTest extends KernelTestCase
{
    use HasBrowser;
    use Form;


    public function testFormNousContacterSansErreur(): void
    {
        $this->remplisFormulaire($this->Browser(), 'http://localhost/nous-contacter')->assertSuccessful();
    }
    public function testFormNousContacterAvecErreurCaptcha(): void
    {
        $this->remplisFormulaire($this->Browser(), 'http://localhost/nous-contacter', true)
            ->AssertStatus(422);
    }
}
