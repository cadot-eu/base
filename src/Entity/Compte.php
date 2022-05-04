<?php

namespace App\Entity;

use App\Entity\TimeTrait;
use App\Repository\CompteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Compte implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimeTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    /**
     * HIDE:{"roles[0]":"ROLE_SUPERADMIN"}
     */
    private $id;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: Types::JSON)]
    /**
     * choice
     * options:["ROLE_USER","ROLE_ADMIN","ROLE_EDITEUR"]
     * TWIG:join(',')
     * OPT:{"multiple":true,"expanded":true}
     * ATTR:{"data-controller":"onecheckbox"}
     */
    private $roles = [];

    #[ORM\Column(type: Types::STRING)]
    /**
     * @var string The hashed password
     */
    private $password;


    #[ORM\Column(type: Types::BOOLEAN)]
    /**
     * choiceenplace
     * options:{"0":"<i class=\"bi bi-toggle-off\"></i>","1":"<i class=\"bi bi-toggle-on\"></i>"}
     */
    private $isVerified = false;

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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
