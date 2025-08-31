<?php

namespace Ledc\GatewayWorker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('gateway:worker', 'GatewayWorker')]
class Command extends SymfonyCommand
{
    /**
     * 允许的动作
     */
    private const array ACTION_LIST = ['start', 'stop', 'restart', 'reload', 'status', 'connections'];

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('action', InputArgument::OPTIONAL, '支持的动作: ' . implode(', ', self::ACTION_LIST));
        $this->addOption('daemon', 'd', InputOption::VALUE_NONE, 'DAEMON mode');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');
        $output->writeln(date('Y-m-d H:i:s') . " <info>GatewayWorker $action...</info>");
        Process::run();
        return self::SUCCESS;
    }
}
