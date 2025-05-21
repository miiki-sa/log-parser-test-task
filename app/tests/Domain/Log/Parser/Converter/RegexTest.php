<?php

declare(strict_types=1);

namespace Tests\Domain\Log\Parser\Converter;

use App\Domain\Log\Parser\Converter\ConverterException;
use App\Domain\Log\Parser\Converter\Regex;
use App\Domain\Log\Parser\Log;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class RegexTest extends TestCase
{
    private Regex $converter;

    protected function setUp(): void
    {
        $this->converter = new Regex();
    }

    public function testParseValidLogLine(): void
    {
        $logLine = 'api-service - - [20/Mar/2024:10:00:00 +0000] "GET /api/v1/users HTTP/1.1" 200';

        $log = $this->converter->parse($logLine);

        $this->assertInstanceOf(Log::class, $log);
        $this->assertSame('api-service', $log->serviceName);
        $this->assertSame('GET /api/v1/users HTTP/1.1', $log->requestLine);
        $this->assertSame(200, $log->statusCode);
        $this->assertInstanceOf(\DateTimeInterface::class, $log->date);
        $this->assertSame('2024-03-20 10:00:00', $log->date->format('Y-m-d H:i:s'));
    }

    public function testParseInvalidLogLine(): void
    {
        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage('Unsupported log entry format "invalid log line"');

        $this->converter->parse('invalid log line');
    }

    public function testParseInvalidDateFormat(): void
    {
        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage('Unsupported date format "d/M/Y:H:i:s O -> 2024-03-20 10:00:00"');

        $this->converter->parse('api-service - - [2024-03-20 10:00:00] "GET /api/v1/users HTTP/1.1" 200');
    }
}
