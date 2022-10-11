<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Annonces;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ApplicationsRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ApplicationsRepository::class)]
#[ApiResource(
    normalizationContext : ['groups' => ['applications:read']],
    denormalizationContext : ['groups' => ['applications:write']],
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
class Applications
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
        Groups(['applications:read', 'applications:write', 'annonces:read', 'user:read']),
    ]
    private ?Annonces $annonce = null;

    #[
        ORM\ManyToOne(inversedBy: 'applications'),
        Groups(['applications:read', 'applications:write', 'annonces:read', 'user:read']),
    ]
    private ?User $applicant = null;

    #[
        ORM\Column,
        Groups(['applications:read', 'applications:write', 'annonces:read', 'user:read']),
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

    public function getApplicant(): ?User
    {
        return $this->applicant;
    }

    public function setApplicant(?User $applicant): self
    {
        $this->applicant = $applicant;

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
