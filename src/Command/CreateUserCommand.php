<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command {
    protected static $defaultName = "app:create-user";

    protected function configure()
    {
        $this->setDescription("New User")
        ->addArgument("email", InputArgument::REQUIRED, "Enter user email.")
        ->addArgument("username", InputArgument::REQUIRED, "Enter username.")
        ->addArgument("password", InputArgument::REQUIRED, "Enter user password.")
        ->addArgument("roles", InputArgument::IS_ARRAY, "Enter user roles.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 1;
    }

}