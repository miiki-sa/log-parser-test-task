<?php

declare(strict_types=1);

namespace App\Domain\Log\Analytic;

interface LogRepositoryInterface
{
    /**
     * @param null|string[] $serviceNames
     */
    public function countWithFilter(
        ?array $serviceNames = null,
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $endDate = null,
        ?int $statusCode = null,
    ): int;
}
