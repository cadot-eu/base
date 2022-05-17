<?php

namespace App\DataFixtures;

use App\Entity\Compte;
use App\Entity\Parametres;
use App\Repository\CarouselRepository;
use App\Repository\CompteRepository;
use App\Repository\ParametresRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Carousel;
use App\Service\base\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Cercueil;
use App\Factory\CarouselFactory;
use App\Factory\CercueilFactory;

use function Zenstruck\Foundry\faker;
use App\Factory\BlogFactory;
use App\Factory\ParametresFactory;

class AdminFixtures extends Fixture
{
    private $compteRepository;

    private $parametresrepository;

    private $passwordHasher;


    protected $fileuploader;

    public function __construct(CompteRepository $compteRepository, ParametresRepository $parametresrepository, UserPasswordHasherInterface $passwordHasher, FileUploader $fileuploader)
    {
        $this->compteRepository = $compteRepository;
        $this->parametresrepository = $parametresrepository;
        $this->passwordHasher = $passwordHasher;
        $this->fileuploader = $fileuploader;
    }

    public function load(ObjectManager $manager): void
    {
        /* -------------------------------------------------------------------------- */
        /*                                 parametres                                 */
        /* -------------------------------------------------------------------------- */
        //ajout des paramères de démo
        $parametres = new Parametres();
        $parametres->setNom("Mail: Email d'envoie");
        $parametres->setValeur("contact@cadot.eu");
        $parametres->setType('simple');
        $manager->persist($parametres);

        $parametres = new Parametres();
        $parametres->setNom("Mail: Nom d'envoie");
        $parametres->setValeur("Enteprise ...");
        $parametres->setType('simple');
        $manager->persist($parametres);

        $docs = ['Mentions légales' => [], 'Conditions générales de vente' => [],  'Livraisons et retours' => []]; //['type' => 'simplelanguage', 'text' => '', 'aide' => '']
        foreach ($docs as $doc => $tab) {
            $type = isset($tab['type']) ? $tab['type'] : '';
            $text = isset($tab['text']) ? $tab['text'] : '';
            $aide = isset($tab['aide']) ? $tab['aide'] : '';
            ParametresFactory::new()->createOne(['nom' => $doc, 'type' => $type, 'valeur' => $text, 'aide' => $aide]);
        }

        /* -------------------------------------------------------------------------- */
        /*                                 compte user                                */
        /* -------------------------------------------------------------------------- */
        //on update l'ancien compte au besoin
        $compte = new Compte();
        $compte->setRoles(['ROLE_SUPERADMIN']);
        $compte->setEmail('m@cadot.eu');
        $compte->setIsVerified(true);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $compte,
            '******'
        );
        $compte->setPassword($hashedPassword);
        $manager->persist($compte);
        $manager->flush();
    }
}
