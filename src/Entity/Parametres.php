<?php

namespace App\Entity;

use App\Entity\TimeTrait;
use App\Repository\ParametresRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ParametresRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Parametres
{
    use TimeTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    /*
     * TPL=no_action_add
     * TPL=no_access_deleted
     * TPL=no_index
     * ORDRE=nom=>ASC
     */
    private $id;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private $nom;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    /*TWIG=striptags|u.truncate(20, '...') */
    private $valeur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(?string $valeur): self
    {
        if (substr($valeur, 0, strlen('<p>') == '<p>') and substr($valeur, 0, -strlen('</p>') == '</p>')) {
            $this->valeur = substr(substr($valeur, strlen('<p>')), 0, -strlen('</p>'));
        } else {
            $this->valeur = $valeur;
        }

        return $this;
    }
}
