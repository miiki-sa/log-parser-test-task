<?php

declare(strict_types=1);

namespace App\Infrastructure\Log\Controller;

use App\Domain\Log\Analytic\AnalyticServiceInterface;
use App\Domain\Log\Analytic\FilterSpecification;
use App\Infrastructure\Log\Dto\CountItemDto;
use App\Infrastructure\Log\Dto\LogQueryFilterDto;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

final class CountFilteredLogsController extends AbstractController
{
    public function __construct(
        private readonly AnalyticServiceInterface $analyticService,
    ) {}

    #[OA\Get(
        path: '/count',
        operationId: 'searchLogs',
        description: 'Count all matching items in the logs.',
        summary: 'Searches logs and provides aggregated count of matches',
        tags: ['analytics'],
        parameters: [
            new OA\Parameter(
                name: 'serviceNames',
                description: 'Array of service names',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string')
                )
            ),
            new OA\Parameter(
                name: 'startDate',
                description: 'Start date',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
            new OA\Parameter(
                name: 'endDate',
                description: 'End date',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
            new OA\Parameter(
                name: 'statusCode',
                description: 'Filter on request status code',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Count of matching results',
                content: new OA\JsonContent(
                    required: ['counter'],
                    properties: [
                        new OA\Property(
                            property: 'counter',
                            description: 'Total count of matching logs',
                            type: 'integer',
                            minimum: 0
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad input parameter'
            ),
        ]
    )]
    #[Route('/count', name: 'count_filtered_logs', methods: ['GET'])]
    public function __invoke(
        #[MapQueryString]
        LogQueryFilterDto $filterDto
    ): JsonResponse {
        $count = $this->analyticService->count(new FilterSpecification(
            $filterDto->serviceNames,
            $filterDto->startDate,
            $filterDto->endDate,
            $filterDto->statusCode
        ));

        return $this->json(
            new CountItemDto($count),
        );
    }
}
