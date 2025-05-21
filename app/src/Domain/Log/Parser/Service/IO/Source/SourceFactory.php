<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Service\IO\Source;

use App\Domain\Log\Parser\LinesIterator\LinesIteratorInterface;
use App\Domain\Log\Parser\LogsIterator\LogsIteratorInterface;

final readonly class SourceFactory implements SourceFactoryInterface
{
    public function __construct(
        private LogsIteratorInterface $logsIterator,
    ) {}

    public function create(LinesIteratorInterface $iterator): Source
    {
        return new Source($this->logsIterator, $iterator);
    }
}
