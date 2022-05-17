<?php

namespace App\Entity;

use App\Repository\CarouselRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\TimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: CarouselRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Carousel
{
    use TimeTrait;
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    /**
     * tpl:no_created
     */
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * simplelanguage
     */
    private $titre;

    /**
     * simplelanguage
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private $texte;

    /**
     * image
     * tpl:index_FileImageNom
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private $image;



    public function getId(): ?int
    {
        return $this->id;
    }



    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }
}
