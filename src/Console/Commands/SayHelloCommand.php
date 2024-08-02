<?php

namespace NDISmate\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SayHelloCommand extends Command
{
    protected static $defaultName = 'app:say-hello';

    protected function configure()
    {
        $this
            ->setDescription('Says Hello')
            ->setHelp('This command allows you to say hello...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello, world!');
        return Command::SUCCESS;
    }
}
