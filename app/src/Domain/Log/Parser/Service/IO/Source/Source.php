<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Service\IO\Source;

use App\Domain\Log\Parser\LinesIterator\LinesIteratorInterface;
use App\Domain\Log\Parser\LogsIterator\LogsIteratorInterface;
use App\Domain\Log\Parser\Service\IO\SourceInterface;

final readonly class Source implements SourceInterface
{
    public function __construct(
        private LogsIteratorInterface $logsIterator,
        private LinesIteratorInterface $linesIterator,
    ) {}

    public function read(): iterable
    {
        yield from $this->logsIterator->from($this->linesIterator);
    }
}
