<?php

namespace App\Infrastructure\Console\DeliveryService;

use App\Application\CommandBus;
use App\Application\DeliveryService\Command\UpdateDeliveryServiceCommand;
use App\Application\DeliveryService\Query\GetAllDeliveryServicesQuery;
use App\Application\QueryBus;
use App\Domain\DeliveryService\Entity\DeliveryService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:delivery-service:update', description: 'Update delivery service console command')]
class UpdateDeliveryServiceConsoleCommand extends Command
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
         * Choice delivery service
         */
        $deliveryServices = $this->queryBus->handle(new GetAllDeliveryServicesQuery());
        $choiceList = new ChoiceQuestion(
            'Please select your delivery service',
            array_map(function (DeliveryService $deliveryService) {
                return $deliveryService->getCode();
            }, $deliveryServices),
            null
        );
        $choiceList->setErrorMessage("Selected delivery service %s is invalid.\n");
        $code = $helper->ask($input, $output, $choiceList);

        /**
         * Set new name for selected delivery service
         */
        $setNameQuestion = new Question("Set new name (press enter for skip stage) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (strlen($answer) > 100) {
                throw new \RuntimeException(
                    'The name field must be less than 100 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set status for selected delivery service
         */
        $setIsActiveQuestion = new Question("Set is_active (press enter for skip stage) \n", null);
        $setIsActiveQuestion->setValidator(function ($answer) {
            if (is_null($answer)) {
                return null;
            }

            $answer = filter_var($answer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!is_bool($answer)) {
                throw new \RuntimeException(
                    'The is_active field must contain a boolean value'
                );
            }

            return $answer;
        });
        $isActive = $helper->ask($input, $output, $setIsActiveQuestion);

        $this->commandBus->dispatch(new UpdateDeliveryServiceCommand($code, $name, $isActive));

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

        if (!is_null($name) || !is_null($isActive)) {
            $output->writeln(sprintf('Delivery service with code %s successfully updated', $code));
        } else {
            $output->writeln(sprintf('Delivery service with code %s not updated', $code));
        }

        return Command::SUCCESS;
    }
}
