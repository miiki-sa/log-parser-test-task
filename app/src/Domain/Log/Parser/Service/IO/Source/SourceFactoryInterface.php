<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Service\IO\Source;

use App\Domain\Log\Parser\LinesIterator\LinesIteratorInterface;

interface SourceFactoryInterface
{
    public function create(LinesIteratorInterface $iterator): Source;
}
