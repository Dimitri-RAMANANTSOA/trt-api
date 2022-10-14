<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Annonces;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\UserOwnedInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ApplicationsRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ApplicationsRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN') or user.isActive == 1",
    normalizationContext : ['groups' => ['applications:read']],
    //denormalizationContext : ['groups' => ['']],
    paginationItemsPerPage : 10,
    paginationMaximumItemsPerPage : 100,
    paginationClientItemsPerPage : true,
    operations: [
        new Get(),
        new Post(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_CANDIDAT')",
            denormalizationContext : ['groups' => ['applications:write']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_CONSULTANT')",
            denormalizationContext : ['groups' => ['']]
        ),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.user == user"),
        new GetCollection(),
    ]
)]
class Applications implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[
        ORM\Column,
        Groups(['applications:read', 'annonces:read', 'user:read']),
    ]
    private ?int $id = null;

    #[
        ORM\ManyToOne(inversedBy: 'applications'),
        Groups(['applications:read', 'applications:write', 'annonces:read', 'user:read', 'admin:write']),
    ]
    private ?Annonces $annonce = null;

    #[
        ORM\ManyToOne(inversedBy: 'applications'),
        Groups(['applications:read', 'annonces:read', 'user:read', 'admin:write']),
    ]
    public ?User $user = null;

    #[
        ORM\Column,
        Groups(['applications:read', 'applications:write', 'annonces:read', 'user:read', 'consultant:write', 'admin:write']),
    ]
    private ?bool $isValidate = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnonce(): ?Annonces
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonces $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isIsValidate(): ?bool
    {
        return $this->isValidate;
    }

    public function setIsValidate(bool $isValidate): self
    {
        $this->isValidate = $isValidate;

        return $this;
    }
}
