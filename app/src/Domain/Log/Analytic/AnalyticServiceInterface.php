<?php

declare(strict_types=1);

namespace App\Domain\Log\Analytic;

interface AnalyticServiceInterface
{
    public function count(FilterSpecification $filterSpecification): int;
}
