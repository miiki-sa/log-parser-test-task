<?php

declare(strict_types=1);

namespace App\Infrastructure\Log\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LogQueryFilterDto
{
    /**
     * @param null|string[] $serviceNames
     */
    public function __construct(
        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\Type('string'),
            new Assert\NotBlank(),
        ])]
        public ?array $serviceNames = null,
        public ?\DateTimeImmutable $startDate = null,
        #[Assert\GreaterThanOrEqual(propertyPath: 'startDate')]
        public ?\DateTimeImmutable $endDate = null,
        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public ?int $statusCode = null,
    ) {}
}
