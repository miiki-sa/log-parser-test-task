<?php

declare(strict_types=1);

namespace Tests\Domain\Log\Parser;

use App\Domain\Log\Parser\Log;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class LogTest extends TestCase
{
    public function testLogCreation(): void
    {
        $serviceName = 'test-service';
        $date = new \DateTimeImmutable('2024-03-20 10:00:00');
        $requestLine = 'GET /api/v1/users HTTP/1.1';
        $statusCode = 200;

        $log = new Log(
            serviceName: $serviceName,
            date: $date,
            requestLine: $requestLine,
            statusCode: $statusCode
        );

        $this->assertSame($serviceName, $log->serviceName);
        $this->assertSame($date, $log->date);
        $this->assertSame($requestLine, $log->requestLine);
        $this->assertSame($statusCode, $log->statusCode);
    }
}
