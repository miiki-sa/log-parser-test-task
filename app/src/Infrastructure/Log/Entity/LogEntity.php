<?php

declare(strict_types=1);

namespace App\Infrastructure\Log\Entity;

use App\Infrastructure\Log\Repository\Destination;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Destination::class)]
#[ORM\Index(name: 'idx_log_service_name', columns: ['service_name'])]
#[ORM\Index(name: 'idx_log_timestamp', columns: ['timestamp'])]
#[ORM\Index(name: 'idx_log_status_code', columns: ['status_code'])]
#[ORM\Index(name: 'idx_log_timestamp_service', columns: ['timestamp', 'service_name'])]
#[ORM\Table(name: 'log')]
class LogEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    /** @phpstan-ignore-next-line property.onlyRead */
    private int $id;

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        protected string $serviceName,
        #[ORM\Column(type: 'datetime_immutable')]
        protected \DateTimeInterface $timestamp,
        #[ORM\Column(type: 'string', length: 255)]
        protected string $requestLine,
        #[ORM\Column(type: 'integer')]
        protected int $statusCode,
    ) {}
}
