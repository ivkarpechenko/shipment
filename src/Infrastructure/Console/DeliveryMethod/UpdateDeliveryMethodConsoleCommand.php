<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\DeliveryMethod;

use App\Application\CommandBus;
use App\Application\DeliveryMethod\Command\UpdateDeliveryMethodCommand;
use App\Application\DeliveryMethod\Query\GetAllDeliveryMethodQuery;
use App\Application\QueryBus;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:delivery-method:update', description: 'Update delivery method console command')]
class UpdateDeliveryMethodConsoleCommand extends Command
{
    public function __construct(
        public readonly QueryBus $queryBus,
        public readonly CommandBus $commandBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        /**
         * Choice delivery method
         */
        $deliveryMethods = $this->queryBus->handle(new GetAllDeliveryMethodQuery());
        $choiceList = new ChoiceQuestion(
            'Please select your delivery method',
            array_map(function (DeliveryMethod $deliveryMethod) {
                return $deliveryMethod->getCode();
            }, $deliveryMethods),
            null
        );
        $choiceList->setErrorMessage("Selected delivery method %s is invalid.\n");
        $code = $helper->ask($input, $output, $choiceList);

        /**
         * Set new name for selected delivery method
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
         * Set status for selected delivery method
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

        $this->commandBus->dispatch(new UpdateDeliveryMethodCommand($code, $name, $isActive));

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

        if (!is_null($name) || !is_null($isActive)) {
            $output->writeln(sprintf('Delivery method with code %s successfully updated', $code));
        } else {
            $output->writeln(sprintf('Delivery method with code %s not updated', $code));
        }

        return Command::SUCCESS;
    }
}
