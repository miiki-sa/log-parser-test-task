<?php

declare(strict_types=1);

namespace Tests\Domain\Log\Analytic;

use App\Domain\Log\Analytic\FilterSpecification;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class FilterSpecificationTest extends TestCase
{
    public function testCreateWithAllParameters(): void
    {
        $serviceNames = ['service1', 'service2'];
        $startDate = new \DateTimeImmutable('2024-03-20 10:00:00');
        $endDate = new \DateTimeImmutable('2024-03-20 11:00:00');
        $statusCode = 200;

        $spec = new FilterSpecification(
            serviceNames: $serviceNames,
            startDate: $startDate,
            endDate: $endDate,
            statusCode: $statusCode
        );

        $this->assertSame($serviceNames, $spec->serviceNames);
        $this->assertSame($startDate, $spec->startDate);
        $this->assertSame($endDate, $spec->endDate);
        $this->assertSame($statusCode, $spec->statusCode);
    }

    public function testCreateWithNoParameters(): void
    {
        $spec = new FilterSpecification();

        $this->assertNull($spec->serviceNames);
        $this->assertNull($spec->startDate);
        $this->assertNull($spec->endDate);
        $this->assertNull($spec->statusCode);
    }

    public function testCreateWithPartialParameters(): void
    {
        $serviceNames = ['service1'];
        $statusCode = 404;

        $spec = new FilterSpecification(
            serviceNames: $serviceNames,
            statusCode: $statusCode
        );

        $this->assertSame($serviceNames, $spec->serviceNames);
        $this->assertNull($spec->startDate);
        $this->assertNull($spec->endDate);
        $this->assertSame($statusCode, $spec->statusCode);
    }
}
