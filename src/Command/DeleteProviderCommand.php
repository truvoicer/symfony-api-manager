<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteProviderCommand extends Command {
    protected static $defaultName = "app:delete-provider";

    protected function configure()
    {
        $this->setDescription("Delete provider")
        ->addArgument("name", InputArgument::REQUIRED, "Enter provider name.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 1;
    }

}