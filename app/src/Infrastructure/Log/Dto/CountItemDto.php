<?php

declare(strict_types=1);

namespace App\Infrastructure\Log\Dto;

readonly class CountItemDto
{
    public function __construct(
        public int $counter
    ) {}
}
