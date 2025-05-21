<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Service\IO;

use App\Domain\Log\Parser\Log;

interface DestinationInterface
{
    public function write(Log ...$logs): void;
}
