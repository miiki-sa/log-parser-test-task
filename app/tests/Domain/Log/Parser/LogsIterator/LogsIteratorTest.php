<?php

declare(strict_types=1);

namespace Tests\Domain\Log\Parser\LogsIterator;

use App\Domain\Log\Parser\Converter\ConverterException;
use App\Domain\Log\Parser\Converter\ConverterInterface;
use App\Domain\Log\Parser\LinesIterator\LinesIteratorInterface;
use App\Domain\Log\Parser\Log;
use App\Domain\Log\Parser\LogsIterator\LogsIterator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversNothing
 */
final class LogsIteratorTest extends TestCase
{
    private LogsIterator $iterator;
    private ConverterInterface $converter;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->converter = $this->createMock(ConverterInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->iterator = new LogsIterator($this->converter, $this->logger);
    }

    public function testIterateValidLogs(): void
    {
        $inputLines = [
            'valid line 1',
            'valid line 2',
        ];

        $expectedLogs = [
            new Log('service1', new \DateTimeImmutable(), 'GET /1', 200),
            new Log('service2', new \DateTimeImmutable(), 'GET /2', 201),
        ];

        $linesIterator = $this->createMock(LinesIteratorInterface::class);
        $linesIterator->method('lines')
            ->willReturn(new \ArrayIterator($inputLines))
        ;

        $this->converter->expects($this->exactly(2))
            ->method('parse')
            ->willReturnOnConsecutiveCalls(...$expectedLogs)
        ;

        $logs = iterator_to_array($this->iterator->from($linesIterator));

        $this->assertCount(2, $logs);
        $this->assertEquals($expectedLogs, $logs);
    }

    public function testSkipInvalidLogs(): void
    {
        $inputLines = [
            'valid line',
            'invalid line',
            'valid line 2',
        ];

        $expectedLogs = [
            new Log('service1', new \DateTimeImmutable(), 'GET /1', 200),
            new Log('service2', new \DateTimeImmutable(), 'GET /2', 201),
        ];

        $linesIterator = $this->createMock(LinesIteratorInterface::class);
        $linesIterator->method('lines')
            ->willReturn(new \ArrayIterator($inputLines))
        ;

        $this->converter->expects($this->exactly(3))
            ->method('parse')
            ->willReturnCallback(function ($line) use ($expectedLogs) {
                if ('invalid line' === $line) {
                    throw new ConverterException('Invalid log format');
                }

                return 'valid line' === $line ? $expectedLogs[0] : $expectedLogs[1];
            })
        ;

        $this->logger->expects($this->once())
            ->method('info')
            ->with(
                'Log entry parsing error.',
                $this->callback(function ($context) {
                    return isset($context['exception'])
                        && $context['exception'] instanceof ConverterException
                        && 'Invalid log format' === $context['exception']->getMessage()
                        && isset($context['line'])
                        && 1 === $context['line'];
                })
            )
        ;

        $logs = iterator_to_array($this->iterator->from($linesIterator));

        $this->assertCount(2, $logs);
        $this->assertEquals($expectedLogs, $logs);
    }
}
