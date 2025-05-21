<?php

declare(strict_types=1);

namespace App\Domain\Log\Analytic;

final readonly class FilterSpecification
{
    /**
     * @param null|string[] $serviceNames
     */
    public function __construct(
        public ?array $serviceNames = null,
        public ?\DateTimeImmutable $startDate = null,
        public ?\DateTimeImmutable $endDate = null,
        public ?int $statusCode = null,
    ) {}
}
