<?php
// api/src/Serializer/ContextBuilder.php
namespace App\Serializer;

use App\Entity\User;
use App\Entity\Annonces;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;
    private $tokenStorage;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        //dd($request, $context, $extractedAttributes);
        //dd($this->tokenStorage->getToken()->getUser()->getId());
        //$resourceClass === Annonces::class &&
        
        if(
        $resourceClass === User::class &&
        $context["uri_variables"]["id"] == $this->tokenStorage->getToken()->getUser()->getId()
        ) {
            $context['groups'][] = 'user:write';
        }

        if (
        $this->authorizationChecker->isGranted('ROLE_RECRUTEUR') && 
        ($extractedAttributes['operation'] instanceof Post || $extractedAttributes['operation'] instanceof Patch)
        ) {
            $context['groups'][] = 'recruteur:write';
        }

        if (
        $this->authorizationChecker->isGranted('ROLE_ADMIN') && 
        ($extractedAttributes['operation'] instanceof Post || $extractedAttributes['operation'] instanceof Patch)
        ) {
            $context['groups'][] = 'admin:write';
        }

        if (
        $this->authorizationChecker->isGranted('ROLE_CONSULTANT') && 
        ($extractedAttributes['operation'] instanceof Post || $extractedAttributes['operation'] instanceof Patch)
        ) {
            $context['groups'][] = 'consultant:write';
        }

        return $context;
    }
}