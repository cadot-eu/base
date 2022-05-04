<?php

namespace App\Entity;

use App\Entity\TimeTrait;
use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Categorie
{
    use TimeTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private $nom;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: "categories")]
    /*
     * TPL:no_form
     * TPL:no_index
     */
    private $article;

    #[Gedmo\Slug(fields: ["nom"], unique: true, updatable: true)]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private $slug;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
