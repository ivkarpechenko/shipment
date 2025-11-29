<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\DeliveryMethod;

use App\Application\CommandBus;
use App\Application\DeliveryMethod\Command\CreateDeliveryMethodCommand;
use App\Application\DeliveryMethod\Query\GetAllDeliveryMethodQuery;
use App\Application\QueryBus;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:delivery-method:create', description: 'Create delivery method console command')]
class CreateDeliveryMethodConsoleCommand extends Command
{
    public function __construct(
        public readonly CommandBus $commandBus,
        public readonly QueryBus $queryBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $setCodeQuestion = new Question("Set delivery method code \n", null);
        $setCodeQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 50) {
                throw new \RuntimeException(
                    'The code field is required and must be less than 50 characters'
                );
            }

            if ($answer == trim($answer) && str_contains($answer, ' ')) {
                throw new \RuntimeException(
                    'The code field must be without spaces'
                );
            }

            return $answer;
        });
        $code = strtolower($helper->ask($input, $output, $setCodeQuestion));

        /**
         * Set delivery method code
         */
        $setNameQuestion = new Question("Set delivery method name \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 100) {
                throw new \RuntimeException(
                    'The name field is required and must be less than 100 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        $this->commandBus->dispatch(new CreateDeliveryMethodCommand($code, $name));

        $deliveryMethods = $this->queryBus->handle(new GetAllDeliveryMethodQuery());
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'code', 'name', 'is_active', 'created_at', 'updated_at'])
            ->setRows(array_map(function (DeliveryMethod $deliveryMethod) {
                return [
                    $deliveryMethod->getId()->toRfc4122(),
                    $deliveryMethod->getCode(),
                    $deliveryMethod->getName(),
                    $deliveryMethod->isActive(),
                    $deliveryMethod->getCreatedAt()->format('Y-m-d H:i:s'),
                    $deliveryMethod->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $deliveryMethods));

        $table->render();

        $output->writeln(sprintf('Delivery method with code %s successfully added', $code));

        return Command::SUCCESS;
    }
}
