<?php

declare(strict_types=1);

namespace App\Infrastructure\Log\Command;

use App\Application\Log\Command\SaveLogCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:log:parse',
    description: 'Parse log file and dispatch entries to the system.',
)]
final class LogParseCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Path to the log file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);

        $pathname = $input->getArgument('path');

        if (!is_string($pathname)) {
            $logger->error(sprintf(
                'I\'d like the pathname would be a string, but something strange has passed ヽ(°□°)ノ "type -> %s"',
                gettype($pathname)
            ));

            return Command::FAILURE;
        }

        if (!is_readable($pathname)) {
            $logger->error(sprintf('Log file "%s" does not exist or is not readable. ¯\_(ツ)_/¯', $pathname));

            return Command::FAILURE;
        }

        try {
            $this->commandBus->dispatch(new SaveLogCommand($pathname));
        } catch (\Throwable $e) {
            $logger->error(sprintf('Command dispatch failed, for reason %s. ¯\_(ツ)_/¯', $e->getMessage()), [
                'exception' => $e,
            ]);

            return Command::FAILURE;
        }

        $logger->info('The file will be processed in background ＾◡＾');

        return Command::SUCCESS;
    }
}
