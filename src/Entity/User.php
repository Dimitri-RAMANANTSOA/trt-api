<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\MediaObject;
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
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    //security : "is_granted('ROLE_ADMIN')",
    normalizationContext : ['groups' => ['user:read']],
    denormalizationContext : ['groups' => ['user:write']],
    paginationItemsPerPage : 10,
    paginationMaximumItemsPerPage : 100,
    paginationClientItemsPerPage : true,
    operations: [
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
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
        Groups(['user:read'])
    ]
    private ?int $id = null;

    #[
        ORM\Column(length: 180, unique: true),
        Groups(['user:read', 'user:write','annonces:read']),
        Constraints\NotBlank,
        Constraints\Email,
        Constraints\Length(min: 5, max: 100)
    ]
    private ?string $email = null;

    #[
        ORM\Column,
        Groups(['user:read', 'user:write'])
    ]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[
        ORM\Column,
        Groups(['user:write']),
        Constraints\NotBlank,
        Constraints\Length(min: 8, max: 100)
    ]
    private ?string $password = null;

    #[
        ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true),
        Groups(['user:read', 'user:write','annonces:read'])
    ]
    private ?MediaObject $media = null;

    #[
        ORM\ManyToMany(targetEntity: Annonces::class, mappedBy: 'applicants'),
        Groups(['user:read', 'user:write','annonces:read'])
    ]
    private Collection $annonces;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
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

    /**
     * @return Collection<int, Annonces>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonces $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->addApplicant($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonces $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            $annonce->removeApplicant($this);
        }

        return $this;
    }
}
