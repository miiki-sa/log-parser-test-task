<?php

declare(strict_types=1);

namespace App\Infrastructure\Log\Repository;

use App\Domain\Log\Parser\Log;
use App\Domain\Log\Parser\Service\IO\DestinationInterface;
use App\Infrastructure\Log\Entity\LogEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogEntity>
 */
final class Destination extends ServiceEntityRepository implements DestinationInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntity::class);
    }

    public function write(Log ...$logs): void
    {
        $entityManager = $this->getEntityManager();

        foreach ($logs as $log) {
            $entityManager->persist(new LogEntity(
                $log->serviceName,
                $log->date,
                $log->requestLine,
                $log->statusCode,
            ));
        }

        $entityManager->flush();
    }
}
