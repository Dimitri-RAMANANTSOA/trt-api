<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Applications;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AnnoncesRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnnoncesRepository::class)]
#[ApiResource(
    security : "is_granted('ROLE_USER')",
    normalizationContext : ['groups' => ['annonces:read']],
    denormalizationContext : ['groups' => ['annonces:write']],
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
class Annonces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[
        ORM\Column,
        Groups(['annonces:read', 'applications:read']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(length: 255),
        Groups(['annonces:read', 'admin:write', 'recruteur:write', 'applications:read']),
    ]
    private ?string $title = null;

    #[
        ORM\Column(length: 255),
        Groups(['annonces:read', 'admin:write', 'recruteur:write', 'applications:read']),
    ]
    private ?string $place = null;

    #[
        ORM\Column(length: 255),
        Groups(['annonces:read', 'admin:write', 'recruteur:write', 'applications:read']),
    ]
    private ?string $description = null;

    #[
        ORM\Column,
        Groups(['annonces:read', 'admin:write', 'consultant:write', 'applications:read']),
    ]
    private ?bool $isPublished = false;

    #[
        ORM\OneToMany(mappedBy: 'annonce', targetEntity: Applications::class),
        Groups(['annonces:read', 'admin:write', 'consultant:write', 'applications:read']),
    ]
    private Collection $applications;

    public function __construct()
    {
        $this->applicants = new ArrayCollection();
        $this->candidateValidations = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

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
            $application->setAnnonce($this);
        }

        return $this;
    }

    public function removeApplication(Applications $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getAnnonce() === $this) {
                $application->setAnnonce(null);
            }
        }

        return $this;
    }
}
