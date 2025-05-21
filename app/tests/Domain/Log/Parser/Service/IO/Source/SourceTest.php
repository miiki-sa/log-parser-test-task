<?php

declare(strict_types=1);

namespace Tests\Domain\Log\Parser\Service\IO\Source;

use App\Domain\Log\Parser\LinesIterator\LinesIteratorInterface;
use App\Domain\Log\Parser\Log;
use App\Domain\Log\Parser\LogsIterator\LogsIteratorInterface;
use App\Domain\Log\Parser\Service\IO\Source\Source;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class SourceTest extends TestCase
{
    public function testReadLogs(): void
    {
        $logsIterator = $this->createMock(LogsIteratorInterface::class);
        $linesIterator = $this->createMock(LinesIteratorInterface::class);

        $log1 = new Log('service1', new \DateTimeImmutable(), 'GET /1', 200);
        $log2 = new Log('service2', new \DateTimeImmutable(), 'GET /2', 201);

        $logsIterator->method('from')
            ->with($linesIterator)
            ->willReturnCallback(function () use ($log1, $log2) {
                yield $log1;

                yield $log2;
            })
        ;

        $source = new Source($logsIterator, $linesIterator);
        $logs = iterator_to_array($source->read());

        $this->assertCount(2, $logs);
        $this->assertSame($log1, $logs[0]);
        $this->assertSame($log2, $logs[1]);
    }

    public function testReadEmptySource(): void
    {
        $logsIterator = $this->createMock(LogsIteratorInterface::class);
        $linesIterator = $this->createMock(LinesIteratorInterface::class);

        $logsIterator->method('from')
            ->with($linesIterator)
            ->willReturnCallback(function () {
                if (false) {
                    yield null;
                }
            })
        ;

        $source = new Source($logsIterator, $linesIterator);
        $logs = iterator_to_array($source->read());

        $this->assertCount(0, $logs);
    }
}
