<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Service\IO;

use App\Domain\Log\Parser\Log;

interface SourceInterface
{
    /**
     * @return iterable<Log>
     */
    public function read(): iterable;
}
