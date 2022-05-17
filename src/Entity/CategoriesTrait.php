<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

trait CategoriesTrait
{
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }
    #[ORM\ManyToMany(targetEntity: Categorie::class)]
    /**
     * entity
     * label:nom
     * OPT:{"help":"multiple sélection avec CTRL"}
     */
    private $categories;
    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
