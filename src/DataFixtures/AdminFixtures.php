<?php

namespace App\DataFixtures\base;

use App\Entity\Compte;
use App\Entity\Parametres;
use App\Repository\CompteRepository;
use App\Repository\ParametresRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private $compteRepository;

    private $parametresrepository;

    private $passwordHasher;

    public function __construct(CompteRepository $compteRepository, ParametresRepository $parametresrepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->compteRepository = $compteRepository;
        $this->parametresrepository = $parametresrepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        //ajout des paramères de démo
        //on update l'ancien compte au besoin
        if ($ex = $this->parametresrepository->findOneBy([
            'nom' => "Mail: Email d'envoie",
        ])) {
            $parametres = $ex;
        } else {
            //ajout d'utilisateur superadmin
            $parametres = new Parametres();
        }
        $parametres->setNom("Mail: Email d'envoie");
        $parametres->setValeur("contact@cadot.eu");
        $manager->persist($parametres);
        //on update l'ancien compte au besoin
        if ($ex = $this->parametresrepository->findOneBy([
            'nom' => "Mail: Nom d'envoie",
        ])) {
            $parametres = $ex;
        } else {
            //ajout d'utilisateur superadmin
            $parametres = new Parametres();
        }
        $parametres->setNom("Mail: Nom d'envoie");
        $parametres->setValeur("Enteprise ...");
        $manager->persist($parametres);

        //on update l'ancien compte au besoin
        if ($ex = $this->compteRepository->findOneBy([
            'email' => 'm@cadot.eu',
        ])) {
            $compte = $ex;
        } else {
            //ajout d'utilisateur superadmin
            $compte = new Compte();
        }
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
