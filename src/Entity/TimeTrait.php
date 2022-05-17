<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TimeTrait
{
    private function uploadMax()
    {
        $max_upload = (int) (ini_get('upload_max_filesize'));
        $max_post = (int) (ini_get('post_max_size'));
        $memory_limit = (int) (ini_get('memory_limit'));
        return (min($max_upload, $max_post, $memory_limit));
    }

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    /**
     * TPL:no_form
     * opt:{"label":"Supprimé le"}
     */
    private $deletedAt;

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    /**
     * opt:{"label":"Créé le"}
     */
    private $createdAt;

    public function getcreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setcreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    /**
     * opt:{"label":"Mis à jour le"}
     */
    private $updatedAt;

    public function getupdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setupdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
