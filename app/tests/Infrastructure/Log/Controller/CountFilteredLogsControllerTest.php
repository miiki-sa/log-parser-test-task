<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Log\Controller;

use App\Domain\Log\Analytic\AnalyticServiceInterface;
use App\Domain\Log\Analytic\FilterSpecification;
use App\Infrastructure\Log\Controller\CountFilteredLogsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[CoversClass(CountFilteredLogsController::class)]
final class CountFilteredLogsControllerTest extends WebTestCase
{
    private ContainerInterface $container;
    private AnalyticServiceInterface $analyticService;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->container = self::getContainer();

        $this->analyticService = $this->createMock(AnalyticServiceInterface::class);
        $this->container->set(AnalyticServiceInterface::class, $this->analyticService);
    }

    public static function provideFilterCases(): iterable
    {
        yield 'service names only' => [
            ['user-service'],
            null,
            null,
            null,
            15,
        ];

        yield 'service names with start date' => [
            ['user-service'],
            '2024-01-01T00:00:00+00:00',
            null,
            null,
            7,
        ];

        yield 'service names with end date' => [
            ['user-service'],
            null,
            '2024-01-31T23:59:59+00:00',
            null,
            7,
        ];

        yield 'status code only' => [
            null,
            null,
            null,
            404,
            3,
        ];

        yield 'combined filters' => [
            ['user-service'],
            '2024-01-01T00:00:00+00:00',
            '2024-01-31T23:59:59+00:00',
            200,
            25,
        ];
    }

    #[DataProvider('provideFilterCases')]
    public function testCountWithFilters(
        ?array $serviceNames,
        ?string $startDate,
        ?string $endDate,
        ?int $statusCode,
        int $expectedCount
    ): void {
        $actualSpec = null;
        $expectedSpec = new FilterSpecification(
            serviceNames: $serviceNames,
            startDate: $startDate ? new \DateTimeImmutable($startDate) : null,
            endDate: $endDate ? new \DateTimeImmutable($endDate) : null,
            statusCode: $statusCode,
        );

        $this->analyticService
            ->expects($this->once())
            ->method('count')
            ->with(self::callback(function (FilterSpecification $spec) use (&$actualSpec) {
                $actualSpec = $spec;

                return true;
            }))
            ->willReturn($expectedCount)
        ;

        $this->client->request('GET', '/count', array_filter([
            'serviceNames' => $serviceNames,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusCode' => $statusCode,
        ]));

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertEquals($expectedSpec, $actualSpec, 'Filter specification was not properly parsed');
        $this->assertJsonStringEqualsJsonString(
            json_encode(['counter' => $expectedCount], JSON_THROW_ON_ERROR),
            $content
        );
    }

    public function testCountWithEmptyFilter(): void
    {
        $actualSpec = null;
        $expectedSpec = new FilterSpecification();

        $this->analyticService
            ->expects($this->once())
            ->method('count')
            ->with(self::callback(function (FilterSpecification $spec) use (&$actualSpec) {
                $actualSpec = $spec;

                return true;
            }))
            ->willReturn(42)
        ;

        $this->client->request('GET', '/count');

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson($content);
        $this->assertEquals($expectedSpec, $actualSpec, 'Filter specification was not properly parsed');
        $this->assertJsonStringEqualsJsonString(
            json_encode(['counter' => 42], JSON_THROW_ON_ERROR),
            $content
        );
    }
}
