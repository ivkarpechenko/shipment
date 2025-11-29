<?php

declare(strict_types=1);

namespace App\Application;

use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerCommandBus implements CommandBus
{
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function dispatch(Command $command): mixed
    {
        return $this->commandBus->dispatch($command);
    }
}
