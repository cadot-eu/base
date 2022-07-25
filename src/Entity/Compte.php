<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use App\Entity\base\TimeTrait;
use App\Entity\base\EtatTrait;
use App\Entity\base\VerifiedTrait;
use App\Entity\base\ActifTrait;
use App\Entity\base\SituationTrait;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[UniqueEntity(fields: ['email'], message: 'Merci de contacter picbleu andre@picbleu.fr')]
/**
 * crud type
 */
class Compte implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimeTrait;
    use SituationTrait;
    use VerifiedTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    /**
     * HIDE:{"roles[0]":"ROLE_SUPERADMIN"}
     * TPL:no_created
     * TPL:no_updated
     */
    private $id;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: Types::JSON)]
    /**
     * choice
     * options:{"client":"ROLE_USER","administrateur":"ROLE_ADMIN","partenaire":"ROLE_PARTENAIRE"}
     * TWIG:join(',')
     * OPT:{"multiple":true,"expanded":true}
     */
    private $roles = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    /**
     * TPL:no_index
     * TPL:no_form
     */
    private $password;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $nom;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!$roles) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {


        $this->password = $password;

        return $this;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }



    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
