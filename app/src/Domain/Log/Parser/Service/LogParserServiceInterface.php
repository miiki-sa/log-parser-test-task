<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Service;

use App\Domain\Log\Parser\Service\IO\DestinationInterface;
use App\Domain\Log\Parser\Service\IO\SourceInterface;

interface LogParserServiceInterface
{
    public function pipe(SourceInterface $source, DestinationInterface $destination): void;
}
