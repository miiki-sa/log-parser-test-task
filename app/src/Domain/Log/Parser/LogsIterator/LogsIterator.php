<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\LogsIterator;

use App\Domain\Log\Parser\Converter\ConverterInterface;
use App\Domain\Log\Parser\LinesIterator\LinesIteratorInterface;
use Psr\Log\LoggerInterface;

final readonly class LogsIterator implements LogsIteratorInterface
{
    public function __construct(
        private ConverterInterface $converter,
        private LoggerInterface $logger,
    ) {}

    public function from(LinesIteratorInterface $iterator): \Generator
    {
        foreach ($iterator->lines() as $i => $line) {
            try {
                yield $this->converter->parse($line);
            } catch (\Throwable $e) {
                $this->logger->info('Log entry parsing error.', ['exception' => $e, 'line' => $i]);
            }
        }
    }
}
