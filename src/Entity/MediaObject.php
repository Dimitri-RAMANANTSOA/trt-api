<?php
// api/src/Entity/MediaObject.php
namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\CreateMediaObjectAction;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['media_object:read']], 
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(),
        new Delete(),
        new GetCollection(),
        new Post(
            controller: CreateMediaObjectAction::class, 
            deserialize: false, 
            validationContext: ['groups' => ['Default', 'media_object_create']], 
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object', 
                                'properties' => [
                                    'file' => [
                                        'type' => 'string', 
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        )
    ]
)]
class MediaObject
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['media_object:read', 'annonces:read'])]
    private ?int $id = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['media_object:read', 'user:read','annonces:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: "media_object", fileNameProperty: "filePath")]
    #[
        Assert\NotNull(groups: ['media_object_create']),
        Assert\File(
            maxSize: '1024k',
            mimeTypes: ['application/pdf', 'application/x-pdf'],
            mimeTypesMessage: 'Please upload a valid PDF',
        )
    ]
    public ?File $file = null;

    #[ORM\Column(nullable: true)] 
    public ?string $filePath = null;

    #[
        ORM\OneToOne(mappedBy: 'media'),
        Groups(['media_object:read', 'user:read', 'annonces:read'])
    ]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setMedia(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getMedia() !== $this) {
            $user->setMedia($this);
        }

        $this->user = $user;

        return $this;
    }
}