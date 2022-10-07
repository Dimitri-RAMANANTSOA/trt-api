<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AnnoncesRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnnoncesRepository::class)]
#[ApiResource(
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
    #[ORM\Column]
    private ?int $id = null;

    #[
        ORM\Column(length: 255),
        Groups(['annonces:read', 'annonces:write']),
    ]
    private ?string $title = null;

    #[
        ORM\Column(length: 255),
        Groups(['annonces:read', 'annoncesannonces:write']),
    ]
    private ?string $place = null;

    #[
        ORM\Column(length: 255),
        Groups(['annonces:read', 'annonces:write']),
    ]
    private ?string $description = null;

    #[
        ORM\Column,
        Groups(['annonces:read', 'annonces:write']),
    ]
    private ?bool $isPublished = false;

    #[
        ORM\ManyToMany(targetEntity: User::class, inversedBy: 'annonces'),
        Groups(['annonces:read', 'annonces:write']),
    ]
    private Collection $applicants;

    public function __construct()
    {
        $this->applicants = new ArrayCollection();
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
     * @return Collection<int, User>
     */
    public function getApplicants(): Collection
    {
        return $this->applicants;
    }

    public function addApplicant(User $applicant): self
    {
        if (!$this->applicants->contains($applicant)) {
            $this->applicants->add($applicant);
        }

        return $this;
    }

    public function removeApplicant(User $applicant): self
    {
        $this->applicants->removeElement($applicant);

        return $this;
    }
}
