<?php

namespace App\Tests;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\PantherTestCaseTrait;
use Zenstruck\Browser\PantherBrowser;
use Symfony\Component\Panther\PantherTestCase;
use Zenstruck\Browser\Test\HasBrowser;

/** @var \Zenstruck\Browser\PantherBrowser $pantherBrowser **/
class AdminTest extends PantherTestCase
{
    use HasBrowser;
    public function testLoginLogout(): void
    {
        $this->Browser()
            ->visit('/admin/accueil')
            ->fillField('email', '')
            ->fillField('password', '')
            ->click('Connexion')
            ->assertSee('Tableau de bord')
            ->visit('/deconnexion')
            ->assertNotSeeElement('.zone');
    }
    public function testVisitMenuAdmin(): void
    {
        $parts = ['compte', 'parametres', 'links', 'categorie', 'carousel', 'article', 'comment', 'entreprise', 'comparatif', 'dossier', 'glossaire', 'question', 'produit', 'groupe'];
        $b = $this->Browser()
            ->visit('/admin/accueil')
            ->fillField('email', '')
            ->fillField('password', '')
            ->click('Connexion')
            ->assertSee('Tableau de bord');
        foreach ($parts as $part) {
            $b->visit('/admin/' . $part)
                ->assertSeeIn('h1', $part);
        }
    }
    public function testAffichageDesArticles(): void
    {
        $this->Browser()
            ->visit('/admin/accueil')
            ->fillField('email', '')
            ->fillField('password', '')
            ->click('Connexion')
            ->assertSee('Tableau de bord')
            ->visit('/admin/article')
            ->assertElementCount('table tbody tr', 10);
    }

    public function testCreerUnArticleEtRecherche(): void
    {
        $this->Browser()
            ->visit('/admin/accueil')
            ->fillField('email', '')
            ->fillField('password', '')
            ->click('Connexion')
            ->visit('/admin/article/?filterField=r.recherche&filterValue=&sort=a.updatedAt&direction=desc&page=1')
            ->assertNotSee('TestBachibouzouk')
            ->visit('/admin/article/new')
            ->assertSee('Créer Article')
            ->fillField('article[titre]', 'TestBachibouzouk')
            ->fillField('article[keywords]', 'mot chapeau')
            ->fillField('article[article]', '<h1>Super titre</h1>description')
            ->selectFieldOption('article[etat]', 'en ligne')
            ->selectFieldOptions('article_categories', ['Argent et finances', 'Bois énergie'])
            ->click('Créer')
            ->visit('/admin/article/?filterField=r.recherche&filterValue=&sort=a.updatedAt&direction=desc&page=1')
            ->assertSee('TestBachibouzouk');
    }
}
