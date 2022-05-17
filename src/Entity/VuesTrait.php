<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait VuesTrait
{
    #[ORM\Column(type: Types::INTEGER)]
    /**
     * TPL=no_form
     * class=text-center
     */
    private $vues = 0;
    public function getVues(): ?int
    {
        return $this->vues;
    }

    public function setVues(int $vues): self
    {
        $this->vues = $vues;

        return $this;
    }
}
