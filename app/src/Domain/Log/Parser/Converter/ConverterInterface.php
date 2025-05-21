<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Converter;

use App\Domain\Log\Parser\Log;

interface ConverterInterface
{
    /**
     * @throws ConverterException
     */
    public function parse(string $log): Log;
}
