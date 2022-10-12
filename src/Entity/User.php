<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\MediaObject;
use App\Entity\Applications;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Validator\AdminGroupsGenerator;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Validator as AcmeAssert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN') or user.isActive == 1",
    validationContext: ['groups' => AdminGroupsGenerator::class],
    normalizationContext : ['groups' => ['user:read']],
    //denormalizationContext : ['groups' => ['user:write']],
    paginationItemsPerPage : 10,
    paginationMaximumItemsPerPage : 100,
    paginationClientItemsPerPage : true,
    operations: [
        new Get(),
        new Post(),
        new Patch(),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(),
    ]
)]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[
        ORM\Column,
        Groups(['user:read', 'applications:read'])
    ]
    private ?int $id = null;

    #[
        ORM\Column(length: 180, unique: true),
        Groups(['user:read', 'applications:read', 'user:write', 'admin:write']),
        Assert\NotBlank,
        Assert\Email,
        Assert\Length(min: 5, max: 100)
    ]
    public ?string $email = null;

    #[
        ORM\Column,
        Groups(['user:read', 'user:write','admin:write']),
        AcmeAssert\ContainsInArray(
            forbiddenvalues: ["ROLE_ADMIN", "ROLE_CONSULTANT"],
            groups: ['user']
        )
    ]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[
        ORM\Column,
        Groups(['user:write', 'admin:write']),
        Assert\NotBlank,
        Assert\Length(min: 8, max: 100)
    ]
    private ?string $password = null;

    #[
        ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true),
        Groups(['user:read', 'user:write', 'applications:read', 'admin:write'])
    ]
    private ?MediaObject $media = null;

    #[
        ORM\Column(length: 255),
        Groups(['user:read','applications:read', 'user:write', 'admin:write']),
    ]
    private ?string $firstname = null;

    #[
        ORM\Column(length: 255),
        Groups(['user:read', 'user:write', 'applications:read', 'admin:write']),
    ]
    private ?string $lastname = null;

    #[
        ORM\Column(length: 255),
        Groups(['user:read', 'user:write', 'applications:read', 'admin:write']),
    ]
    private ?string $entrepriseaddress = null;

    #[
        ORM\OneToMany(mappedBy: 'applicant', targetEntity: Applications::class, cascade: ['remove']),
        Groups(['user:read', 'applications:read', 'user:write', 'admin:write']),
    ]
    private Collection $applications;

    #[
        ORM\Column,
        Groups(['user:read', 'applications:read', 'consultant:write', 'admin:write'])
    ]
    public ?bool $isActive = false;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
        $this->candidateValidations = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $plaintextPassword): self
    {
        $this->passwordHasherFactory = new PasswordHasherFactory([
            // auto hasher with default options for the User class (and children)
            self::class => ['algorithm' => 'auto']
        ]);

        $passwordHasher = new UserPasswordHasher($this->passwordHasherFactory);

        // hash the password (based on the password hasher factory config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $this,
            $plaintextPassword
        );

        $this->password = $hashedPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getMedia(): ?MediaObject
    {
        return $this->media;
    }

    public function setMedia(?MediaObject $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEntrepriseaddress(): ?string
    {
        return $this->entrepriseaddress;
    }

    public function setEntrepriseaddress(string $entrepriseaddress): self
    {
        $this->entrepriseaddress = $entrepriseaddress;

        return $this;
    }

    /**
     * @return Collection<int, Applications>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Applications $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setApplicant($this);
        }

        return $this;
    }

    public function removeApplication(Applications $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getApplicant() === $this) {
                $application->setApplicant(null);
            }
        }

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
