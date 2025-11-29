<?php

namespace App\Infrastructure\Console\TariffPlan;

use App\Application\CommandBus;
use App\Application\DeliveryMethod\Query\GetAllDeliveryMethodQuery;
use App\Application\DeliveryService\Query\GetAllDeliveryServicesQuery;
use App\Application\QueryBus;
use App\Application\TariffPlan\Command\CreateTariffPlanCommand;
use App\Application\TariffPlan\Query\GetAllTariffPlansQuery;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\TariffPlan\Entity\TariffPlan;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:tariff-plan:create', description: 'Create tariff plan console command')]
class CreateTariffPlanConsoleCommand extends Command
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
        $deliveryServices = $this->queryBus->handle(new GetAllDeliveryServicesQuery(true));
        $choiceList = new ChoiceQuestion(
            'Please select your delivery service',
            array_map(function (DeliveryService $deliveryService) {
                return $deliveryService->getCode();
            }, $deliveryServices),
            null
        );
        $choiceList->setErrorMessage("Selected delivery service %s is invalid.\n");
        $deliveryServiceCode = $helper->ask($input, $output, $choiceList);

        /**
         * Choice delivery method
         */
        $deliveryMethods = $this->queryBus->handle(new GetAllDeliveryMethodQuery(true));
        $choiceList = new ChoiceQuestion(
            'Please select your delivery method',
            array_map(function (DeliveryMethod $deliveryMethod) {
                return $deliveryMethod->getCode();
            }, $deliveryMethods),
            null
        );
        $choiceList->setErrorMessage("Selected delivery method %s is invalid.\n");
        $deliveryMethodCode = $helper->ask($input, $output, $choiceList);

        /**
         * Set tariff plan code
         */
        $setCodeQuestion = new Question("Set tariff plan code \n", null);
        $setCodeQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 100) {
                throw new \RuntimeException(
                    'The code field is required and must be less than 100 characters'
                );
            }

            if ($answer == trim($answer) && str_contains($answer, ' ')) {
                throw new \RuntimeException(
                    'The code field must be without spaces'
                );
            }

            return $answer;
        });
        $code = $helper->ask($input, $output, $setCodeQuestion);

        /**
         * Set tariff plan name
         */
        $setNameQuestion = new Question("Set tariff plan name \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 255) {
                throw new \RuntimeException(
                    'The name field is required and must be less than 255 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        $this->commandBus->dispatch(new CreateTariffPlanCommand($deliveryServiceCode, $deliveryMethodCode, $code, $name));

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

        $output->writeln(sprintf('Tariff plan with code %s successfully added', $code));

        return Command::SUCCESS;
    }
}
