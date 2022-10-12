<?php
// api/src/Validator/AdminGroupsGenerator.php

namespace App\Validator;

use ApiPlatform\Symfony\Validator\ValidationGroupsGeneratorInterface;
use App\Entity\Book;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AdminGroupsGenerator implements ValidationGroupsGeneratorInterface
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($context): array
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN', $context) ? ['admin'] : ['user'];
    }
}