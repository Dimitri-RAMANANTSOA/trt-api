<?php
// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine;

use App\Entity\Annonces;
use App\Entity\Applications;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Annonces::class == $resourceClass && $this->security->isGranted('ROLE_CANDIDAT')) 
        {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.isPublished = 1', $rootAlias));
        }

        if (Applications::class == $resourceClass && $this->security->isGranted('ROLE_CANDIDAT')) 
        {
            $userId= $this->security->getUser()->getId();
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.user = :value', $rootAlias));
            $queryBuilder->setParameter('value', $userId);
        }

        if (Annonces::class == $resourceClass && $this->security->isGranted('ROLE_RECRUTEUR')) 
        {
            $userId= $this->security->getUser()->getId();
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.user = :value', $rootAlias));
            $queryBuilder->setParameter('value', $userId);
        }

        if (Applications::class == $resourceClass && $this->security->isGranted('ROLE_RECRUTEUR')) 
        {
            $annonces = [];
            foreach($this->security->getUser()->getAnnonces() as $annonce)
            {
                $annonces[] = $annonce->getId();
            }

            $userId= $this->security->getUser()->getId();
            $rootAlias = $queryBuilder->getRootAliases()[0];

            $annoncelist = implode(',',array_unique($annonces));

            $queryBuilder
                ->where($queryBuilder->expr()->IN(sprintf('%s.annonce', $rootAlias), $annoncelist))
                ->andWhere(sprintf('%s.isValidate = 1', $rootAlias))
            ;

        }

        return; 
    }
}