<?php

declare(strict_types=1);

namespace App\Application\Log\CommandHandler;

use App\Application\Log\Command\SaveLogCommand;
use App\Domain\Log\Parser\LinesIterator\PlainFileIterator;
use App\Domain\Log\Parser\Service\IO\DestinationInterface;
use App\Domain\Log\Parser\Service\IO\Source\SourceFactoryInterface;
use App\Domain\Log\Parser\Service\LogParserServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SaveLogCommandHandler
{
    public function __construct(
        private LogParserServiceInterface $logService,
        private SourceFactoryInterface $sourceFactory,
        private DestinationInterface $destination,
    ) {}

    public function __invoke(SaveLogCommand $saveLogCommand): void
    {
        $linesIterator = new PlainFileIterator($saveLogCommand->pathname);

        $this->logService->pipe(
            $this->sourceFactory->create($linesIterator),
            $this->destination
        );
    }
}
