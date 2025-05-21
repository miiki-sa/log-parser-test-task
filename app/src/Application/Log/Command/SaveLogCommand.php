<?php

declare(strict_types=1);

namespace App\Application\Log\Command;

final readonly class SaveLogCommand
{
    public function __construct(
        public string $pathname,
    ) {}
}
