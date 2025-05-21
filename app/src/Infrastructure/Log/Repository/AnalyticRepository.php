<?php

declare(strict_types=1);

namespace App\Infrastructure\Log\Repository;

use App\Domain\Log\Analytic\LogRepositoryInterface;
use App\Infrastructure\Log\Entity\LogEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogEntity>
 */
final class AnalyticRepository extends ServiceEntityRepository implements LogRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntity::class);
    }

    public function countWithFilter(
        ?array $serviceNames = null,
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $endDate = null,
        ?int $statusCode = null
    ): int {
        $queryBuilder = $this->createQueryBuilder('log');

        if ($serviceNames) {
            $queryBuilder->andWhere('log.serviceName IN (:serviceNames)')
                ->setParameter('serviceNames', $serviceNames)
            ;
        }

        if ($startDate) {
            $queryBuilder->andWhere('log.createdAt >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
            ;
        }

        if ($endDate) {
            $queryBuilder->andWhere('log.createdAt <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'))
            ;
        }

        if ($statusCode) {
            $queryBuilder->andWhere('log.statusCode = :statusCode')
                ->setParameter('statusCode', $statusCode)
            ;
        }

        $queryBuilder->select('COUNT(log.id)');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
