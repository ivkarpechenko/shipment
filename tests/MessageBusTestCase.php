<?php

namespace App\Tests;

use App\Application\Command;
use App\Application\MessengerCommandBus;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageBusTestCase extends DoctrineTestCase
{
    protected MessageBusInterface $messageBus;

    protected MessengerCommandBus $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageBus = $this->assembleSymfonyMessageBus();
        $this->commandBus = new MessengerCommandBus($this->messageBus);
    }

    private function assembleSymfonyMessageBus(): MessageBusInterface
    {
        return new class implements MessageBusInterface {
            private Command $dispatchedCommand;

            public function dispatch($message, array $stamps = []): Envelope
            {
                $this->dispatchedCommand = $message;

                return new Envelope($message);
            }

            public function lastDispatchedCommand(): Command
            {
                return $this->dispatchedCommand;
            }
        };
    }
}
