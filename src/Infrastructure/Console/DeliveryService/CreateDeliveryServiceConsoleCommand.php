<?php

namespace App\Infrastructure\Console\DeliveryService;

use App\Application\CommandBus;
use App\Application\DeliveryService\Command\CreateDeliveryServiceCommand;
use App\Application\DeliveryService\Query\GetAllDeliveryServicesQuery;
use App\Application\QueryBus;
use App\Domain\DeliveryService\Entity\DeliveryService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:delivery-service:create', description: 'Create delivery service console command')]
class CreateDeliveryServiceConsoleCommand extends Command
{
    public function __construct(
        public readonly CommandBus $commandBus,
        public readonly QueryBus $queryBus
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        /**
         * Set delivery service code
         */
        $setCodeQuestion = new Question("Set delivery service code \n", null);
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
         * Set delivery service code
         */
        $setNameQuestion = new Question("Set delivery service name \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 100) {
                throw new \RuntimeException(
                    'The name field is required and must be less than 100 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        $this->commandBus->dispatch(new CreateDeliveryServiceCommand($code, $name));

        $deliveryServices = $this->queryBus->handle(new GetAllDeliveryServicesQuery());
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'code', 'name', 'is_active', 'created_at', 'updated_at'])
            ->setRows(array_map(function (DeliveryService $deliveryService) {
                return [
                    $deliveryService->getId()->toRfc4122(),
                    $deliveryService->getCode(),
                    $deliveryService->getName(),
                    $deliveryService->isActive(),
                    $deliveryService->getCreatedAt()->format('Y-m-d H:i:s'),
                    $deliveryService->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $deliveryServices));

        $table->render();

        $output->writeln(sprintf('Delivery service with code %s successfully added', $code));

        return Command::SUCCESS;
    }
}
