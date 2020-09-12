<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProviderCommand extends Command {
    protected static $defaultName = "app:create-provider";

    protected function configure()
    {
        $this->setDescription("New provider")
        ->addArgument("name", InputArgument::REQUIRED, "Enter provider name.")
        ->addArgument("api_base_url", InputArgument::OPTIONAL, "Enter provider api base url.")
        ->addArgument("access_key", InputArgument::OPTIONAL, "Enter provider api access key.")
        ->addArgument("secret_key", InputArgument::OPTIONAL, "Enter provider api secret key.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 1;
    }

}