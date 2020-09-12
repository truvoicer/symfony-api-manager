<?php
namespace App\EventSubscriber;

use App\Command\CreateUserCommand;
use App\Service\UserService;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class CreateUserEventSubscriber implements EventSubscriberInterface
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            ConsoleEvents::COMMAND => [
                ["createUserCommand", 0],
            ]
        ];
    }

    public function createUserCommand(ConsoleCommandEvent $event) {
        $arguments = $event->getInput()->getArguments();
        $input = $event->getInput();
        if ($arguments['command'] == CreateUserCommand::getDefaultName()) {
            if(!$this->userService->createUser([
                "username" => $arguments['username'],
                "email" => $arguments['email'],
                "password" => $arguments['password'],
                "roles" => $arguments['roles'],
                ]
            )) {
                $event->getOutput()->writeln("Error inserting " . $arguments['email']);
                return 0;
            }
            $event->getOutput()->writeln("Successfully inserted " . $arguments['email']);
            return 1;
        }
    }

}
