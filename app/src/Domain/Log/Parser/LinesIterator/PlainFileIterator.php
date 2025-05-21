<?php

declare(strict_types=1);

namespace App\Domain\Log\Parser\LinesIterator;

final readonly class PlainFileIterator implements LinesIteratorInterface
{
    private \SplFileObject $file;

    public function __construct(
        private string $pathname
    ) {
        try {
            $this->file = new \SplFileObject($this->pathname);
            $this->file->setFlags(\SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        } catch (\RuntimeException $e) {
            throw new LinesIteratorException(
                message: sprintf('File is not exists or not readable %s.', $this->pathname),
                previous: $e,
            );
        }
    }

    public function lines(): \Generator
    {
        if (false === $this->file->eof()) {
            $firstLine = str_replace("\xEF\xBB\xBF", '', $this->file->fgets());
            if ($firstLine) {
                yield $firstLine;
            }
        }

        while (false === $this->file->eof()) {
            $line = $this->file->fgets();

            if ('' !== $line) {
                yield $line;
            }
        }
    }
}
