<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Service;

use App\Domain\Log\Parser\Service\IO\DestinationInterface;
use App\Domain\Log\Parser\Service\IO\SourceInterface;

final readonly class LogParserService implements LogParserServiceInterface
{
    public function __construct(
        private int $bufferSize
    ) {
        if ($bufferSize <= 0) {
            throw new \RuntimeException(
                sprintf('Buffer size must be greater than 0, %d given', $this->bufferSize)
            );
        }
    }

    public function pipe(SourceInterface $source, DestinationInterface $destination): void
    {
        $buffer = [];

        foreach ($source->read() as $log) {
            $buffer[] = $log;

            if (count($buffer) >= $this->bufferSize) {
                $destination->write(...$buffer);
                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            $destination->write(...$buffer);
        }
    }
}
