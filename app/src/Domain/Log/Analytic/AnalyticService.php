<?php

declare(strict_types=1);

namespace App\Domain\Log\Analytic;

final readonly class AnalyticService implements AnalyticServiceInterface
{
    public function __construct(
        private LogRepositoryInterface $logRepository,
    ) {}

    public function count(FilterSpecification $filterSpecification): int
    {
        return $this->logRepository->countWithFilter(
            $filterSpecification->serviceNames,
            $filterSpecification->startDate,
            $filterSpecification->endDate,
            $filterSpecification->statusCode
        );
    }
}
