<?php

declare(strict_types=1);

namespace Tests\Domain\Log\Parser\Service;

use App\Domain\Log\Parser\Log;
use App\Domain\Log\Parser\Service\IO\DestinationInterface;
use App\Domain\Log\Parser\Service\IO\SourceInterface;
use App\Domain\Log\Parser\Service\LogParserService;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class LogParserServiceTest extends TestCase
{
    public function testPipeWithBufferSizeOne(): void
    {
        $expectedLogs = [
            new Log('service1', new \DateTimeImmutable(), 'GET /1', 200),
            new Log('service2', new \DateTimeImmutable(), 'GET /2', 201),
        ];

        $expectedWrites = [
            [$expectedLogs[0]],
            [$expectedLogs[1]],
        ];

        $service = new LogParserService(1);
        $source = $this->createMock(SourceInterface::class);
        $destination = $this->createMock(DestinationInterface::class);

        $source->method('read')
            ->willReturn(new \ArrayIterator($expectedLogs))
        ;

        $destination->expects($this->exactly(2))
            ->method('write')
            ->willReturnCallback(function (...$logs) use ($expectedWrites) {
                static $callCount = 0;
                $this->assertEquals($expectedWrites[$callCount], $logs);
                ++$callCount;
            })
        ;

        $service->pipe($source, $destination);
    }

    public function testPipeWithBufferSizeTwo(): void
    {
        $expectedLogs = [
            new Log('service1', new \DateTimeImmutable(), 'GET /1', 200),
            new Log('service2', new \DateTimeImmutable(), 'GET /2', 201),
            new Log('service3', new \DateTimeImmutable(), 'GET /3', 202),
        ];

        $expectedWrites = [
            [$expectedLogs[0], $expectedLogs[1]],
            [$expectedLogs[2]],
        ];

        $service = new LogParserService(2);
        $source = $this->createMock(SourceInterface::class);
        $destination = $this->createMock(DestinationInterface::class);

        $source->method('read')
            ->willReturn(new \ArrayIterator($expectedLogs))
        ;

        $destination->expects($this->exactly(2))
            ->method('write')
            ->willReturnCallback(function (...$logs) use ($expectedWrites) {
                static $callCount = 0;
                $this->assertEquals($expectedWrites[$callCount], $logs);
                ++$callCount;
            })
        ;

        $service->pipe($source, $destination);
    }

    public function testPipeWithEmptySource(): void
    {
        $expectedLogs = [];

        $service = new LogParserService(2);
        $source = $this->createMock(SourceInterface::class);
        $destination = $this->createMock(DestinationInterface::class);

        $source->method('read')
            ->willReturn(new \ArrayIterator($expectedLogs))
        ;

        $destination->expects($this->never())
            ->method('write')
        ;

        $service->pipe($source, $destination);
    }

    public function testInvalidBufferSize(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Buffer size must be greater than 0, 0 given');

        new LogParserService(0);
    }
}
