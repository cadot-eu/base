<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use App\Entity\base\TimeTrait;
use App\Entity\base\VuesTrait;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Categorie
{
    use TimeTrait;
    use VuesTrait;
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    /**
     * tpl:no_created
     * tpl:no_updated
     */
    private $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private $nom;

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


    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Gedmo\Slug(fields: ["nom"], unique: true, updatable: true)]
    /** 
     * TPL:no_index
     * OPT:{"disabled":true}
     * OPT:{"required":false} 
     * OPT:{"label":"url"} 
     */
    private $slug;
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }
}
