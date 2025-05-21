<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser;

final readonly class Log
{
    public function __construct(
        public string $serviceName,
        public \DateTimeInterface $date,
        public string $requestLine,
        public int $statusCode,
    ) {}
}
