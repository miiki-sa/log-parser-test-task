<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\LinesIterator;

interface LinesIteratorInterface
{
    /**
     * @return iterable<string>
     *
     * @throws LinesIteratorException
     */
    public function lines(): iterable;
}
