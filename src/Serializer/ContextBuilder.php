<?php
// api/src/Serializer/ContextBuilder.php
namespace App\Serializer;

use App\Entity\Annonces;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        //dd($context, $resourceClass);

        if (
        $resourceClass === Annonces::class && 
        $this->authorizationChecker->isGranted('ROLE_RECRUTEUR') && 
        ($extractedAttributes['operation'] instanceof Post || $extractedAttributes['operation'] instanceof Patch)
        ) {
            $context['groups'][] = 'recruteur:write';
        }

        if (
        $resourceClass === Annonces::class && 
        $this->authorizationChecker->isGranted('ROLE_ADMIN') && 
        ($extractedAttributes['operation'] instanceof Post || $extractedAttributes['operation'] instanceof Patch)
        ) {
            $context['groups'][] = 'admin:write';
        }

        if ($resourceClass === Annonces::class && 
        $this->authorizationChecker->isGranted('ROLE_CONSULTANT') && 
        ($extractedAttributes['operation'] instanceof Post || $extractedAttributes['operation'] instanceof Patch)
        ) {
            $context['groups'][] = 'consultant:write';
        }

        return $context;
    }
}