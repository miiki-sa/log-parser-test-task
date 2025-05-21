<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\Converter;

use App\Domain\Log\Parser\Log;

class Regex implements ConverterInterface
{
    public function parse(string $log): Log
    {
        $pattern = '/^
                      (?<serviceName>[^\s]+)
                      \s+-\s+-\s+
                      \[(?<date>[^\]]+)\]
                      \s+
                      "(?<requestLine>[^"]+)"
                      \s+
                      (?<statusCode>\d+)
                      /x';

        if (!preg_match($pattern, $log, $matches)) {
            throw new ConverterException(sprintf(
                'Unsupported log entry format "%s"',
                $log
            ));
        }

        $format = 'd/M/Y:H:i:s O';

        $date = \DateTimeImmutable::createFromFormat($format, $matches['date']);

        if (false === $date) {
            throw new ConverterException(sprintf(
                'Unsupported date format "%s -> %s"',
                $format,
                $matches['date']
            ));
        }

        return new Log(
            serviceName: $matches['serviceName'],
            date: $date,
            requestLine: $matches['requestLine'],
            statusCode: (int) $matches['statusCode'],
        );
    }
}
