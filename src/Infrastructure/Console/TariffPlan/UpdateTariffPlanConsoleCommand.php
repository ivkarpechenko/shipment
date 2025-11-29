<?php

namespace App\Infrastructure\Console\TariffPlan;

use App\Application\CommandBus;
use App\Application\QueryBus;
use App\Application\TariffPlan\Command\UpdateTariffPlanCommand;
use App\Application\TariffPlan\Query\GetAllTariffPlansQuery;
use App\Domain\TariffPlan\Entity\TariffPlan;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:tariff-plan:update', description: 'Update tariff plan console command')]
class UpdateTariffPlanConsoleCommand extends Command
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
         * Choice tariff plan
         */
        $tariffPlans = $this->queryBus->handle(new GetAllTariffPlansQuery());
        $choiceList = new ChoiceQuestion(
            'Please select your tariff plan',
            array_map(function (TariffPlan $tariffPlan) {
                return sprintf(
                    '%s|%s|%s',
                    $tariffPlan->getDeliveryService()->getCode(),
                    $tariffPlan->getDeliveryMethod()->getCode(),
                    $tariffPlan->getCode()
                );
            }, $tariffPlans),
            null
        );
        $choiceList->setErrorMessage("Selected tariff plan %s is invalid.\n");
        $choiceAnswer = explode('|', $helper->ask($input, $output, $choiceList));
        $deliveryServiceCode = trim($choiceAnswer[0]);
        $deliveryMethodCode = trim($choiceAnswer[1]);
        $tariffPlanCode = trim($choiceAnswer[2]);

        /**
         * Set new name for selected tariff plan
         */
        $setNameQuestion = new Question("Set new name (press enter for skip stage) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (strlen($answer) > 255) {
                throw new \RuntimeException(
                    'The name field must be less than 255 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set status for selected tariff plan
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

        $this->commandBus->dispatch(new UpdateTariffPlanCommand($deliveryServiceCode, $deliveryMethodCode, $tariffPlanCode, $name, $isActive));

        $tariffPlans = $this->queryBus->handle(new GetAllTariffPlansQuery());
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'delivery_service', 'delivery_method', 'code', 'name', 'is_active', 'created_at', 'updated_at'])
            ->setRows(array_map(function (TariffPlan $tariffPlan) {
                return [
                    $tariffPlan->getId()->toRfc4122(),
                    $tariffPlan->getDeliveryService()->getCode(),
                    $tariffPlan->getDeliveryMethod()->getCode(),
                    $tariffPlan->getCode(),
                    $tariffPlan->getName(),
                    $tariffPlan->isActive(),
                    $tariffPlan->getCreatedAt()->format('Y-m-d H:i:s'),
                    $tariffPlan->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $tariffPlans));

        $table->render();

        if (!is_null($name) || !is_null($isActive)) {
            $output->writeln(sprintf('Tariff plan with code %s successfully updated', $tariffPlanCode));
        } else {
            $output->writeln(sprintf('Tariff plan with code %s not updated', $tariffPlanCode));
        }

        return Command::SUCCESS;
    }
}
