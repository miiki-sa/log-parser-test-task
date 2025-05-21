<?php

declare(strict_types=1);

namespace Tests\Domain\Log\Analytic;

use App\Domain\Log\Analytic\AnalyticService;
use App\Domain\Log\Analytic\FilterSpecification;
use App\Domain\Log\Analytic\LogRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class AnalyticServiceTest extends TestCase
{
    private AnalyticService $service;
    private LogRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(LogRepositoryInterface::class);
        $this->service = new AnalyticService($this->repository);
    }

    public function testCountWithAllFilters(): void
    {
        $serviceNames = ['service1', 'service2'];
        $startDate = new \DateTimeImmutable('2024-03-20 10:00:00');
        $endDate = new \DateTimeImmutable('2024-03-20 11:00:00');
        $statusCode = 200;
        $expectedCount = 42;

        $spec = new FilterSpecification(
            serviceNames: $serviceNames,
            startDate: $startDate,
            endDate: $endDate,
            statusCode: $statusCode
        );

        $this->repository->expects($this->once())
            ->method('countWithFilter')
            ->with($serviceNames, $startDate, $endDate, $statusCode)
            ->willReturn($expectedCount)
        ;

        $count = $this->service->count($spec);

        $this->assertSame($expectedCount, $count);
    }

    public function testCountWithNoFilters(): void
    {
        $expectedCount = 100;

        $spec = new FilterSpecification();

        $this->repository->expects($this->once())
            ->method('countWithFilter')
            ->with(null, null, null, null)
            ->willReturn($expectedCount)
        ;

        $count = $this->service->count($spec);

        $this->assertSame($expectedCount, $count);
    }

    public function testCountWithPartialFilters(): void
    {
        $serviceNames = ['service1'];
        $statusCode = 404;
        $expectedCount = 5;

        $spec = new FilterSpecification(
            serviceNames: $serviceNames,
            statusCode: $statusCode
        );

        $this->repository->expects($this->once())
            ->method('countWithFilter')
            ->with($serviceNames, null, null, $statusCode)
            ->willReturn($expectedCount)
        ;

        $count = $this->service->count($spec);

        $this->assertSame($expectedCount, $count);
    }
}
