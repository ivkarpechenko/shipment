<?php

namespace App\Tests\Application;

use App\Tests\Fixture\CQRS\DummyCommand;
use App\Tests\MessageBusTestCase;

class MessengerCommandBusTest extends MessageBusTestCase
{
    public function testMessageForwardedToMessageBusWhileDispatching(): void
    {
        $command = new DummyCommand();
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }
}
