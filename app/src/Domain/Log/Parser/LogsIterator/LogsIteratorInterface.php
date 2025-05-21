<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\LogsIterator;

use App\Domain\Log\Parser\LinesIterator\LinesIteratorInterface;
use App\Domain\Log\Parser\Log;

interface LogsIteratorInterface
{
    /**
     * @return \Generator<Log>
     */
    public function from(LinesIteratorInterface $iterator): \Generator;
}
