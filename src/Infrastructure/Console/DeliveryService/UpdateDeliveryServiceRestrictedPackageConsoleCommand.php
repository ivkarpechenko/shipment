<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\DeliveryService;

use App\Application\CommandBus;
use App\Application\DeliveryService\Command\UpdateDeliveryServiceRestrictPackageCommand;
use App\Application\DeliveryService\Query\GetAllDeliveryServiceRestrictPackageQuery;
use App\Application\QueryBus;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:delivery-service-restricted-package:update', description: 'Update restrict package for delivery service')]
class UpdateDeliveryServiceRestrictedPackageConsoleCommand extends Command
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
         * Choice delivery service restrict package
         */
        $deliveryServiceRestrictPackages = $this->queryBus->handle(new GetAllDeliveryServiceRestrictPackageQuery());
        $choiceList = new ChoiceQuestion(
            'Please select your restricted package',
            array_map(function (DeliveryServiceRestrictPackage $deliveryServiceRestrictPackage) {
                return $deliveryServiceRestrictPackage->getId()->toRfc4122() . ' | ' . $deliveryServiceRestrictPackage->getDeliveryService()->getName();
            }, $deliveryServiceRestrictPackages),
            null
        );
        $choiceList->setErrorMessage("Selected delivery service restrict package %s is invalid.\n");
        $choiceAnswer = explode('|', $helper->ask($input, $output, $choiceList));
        $deliveryServiceRestrictPackageId = Uuid::fromString(trim($choiceAnswer[0]));
        $deliveryServiceCode = trim($choiceAnswer[1]);

        /**
         * set max weight
         */
        $setNameQuestion = new Question("Set restricted package max weight (in grams) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_INT)) {
                throw new \RuntimeException(
                    'The max weight field is required and must be integer'
                );
            }

            return $answer;
        });
        $maxWeight = (int) $helper->ask($input, $output, $setNameQuestion);

        /**
         * set max width
         */
        $setNameQuestion = new Question("Set restricted package max width (in millimeters) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_INT)) {
                throw new \RuntimeException(
                    'The max width field is required and must be integer'
                );
            }

            return $answer;
        });
        $maxWidth = (int) $helper->ask($input, $output, $setNameQuestion);

        /**
         * set max height
         */
        $setNameQuestion = new Question("Set restricted package max height (in millimeters) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_INT)) {
                throw new \RuntimeException(
                    'The max height field is required and must be integer'
                );
            }

            return $answer;
        });
        $maxHeight = (int) $helper->ask($input, $output, $setNameQuestion);

        /**
         * set max length
         */
        $setNameQuestion = new Question("Set restricted package max length (in millimeters) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_INT)) {
                throw new \RuntimeException(
                    'The max length field is required and must be integer'
                );
            }

            return $answer;
        });
        $maxLength = (int) $helper->ask($input, $output, $setNameQuestion);

        /**
         * set is active
         */
        $setIsActiveQuestion = new ConfirmationQuestion("Active? (y/n) \n", true);
        $isActive = $helper->ask($input, $output, $setIsActiveQuestion);

        $this->commandBus->dispatch(
            new UpdateDeliveryServiceRestrictPackageCommand(
                id: $deliveryServiceRestrictPackageId,
                maxWeight: $maxWeight,
                maxWidth: $maxWidth,
                maxHeight: $maxHeight,
                maxLength: $maxLength,
                isActive: $isActive
            )
        );

        $deliveryServiceRestrictPackages = $this->queryBus->handle(new GetAllDeliveryServiceRestrictPackageQuery());
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'delivery service', 'max weight', 'max width', 'max height', 'max length', 'is_active', 'created', 'updated'])
            ->setRows(array_map(function (DeliveryServiceRestrictPackage $deliveryServiceRestrictPackage) {
                return [
                    $deliveryServiceRestrictPackage->getId()->toRfc4122(),
                    $deliveryServiceRestrictPackage->getDeliveryService()->getCode(),
                    $deliveryServiceRestrictPackage->getMaxWeight(),
                    $deliveryServiceRestrictPackage->getMaxWidth(),
                    $deliveryServiceRestrictPackage->getMaxHeight(),
                    $deliveryServiceRestrictPackage->getMaxLength(),
                    $deliveryServiceRestrictPackage->isActive(),
                    $deliveryServiceRestrictPackage->getCreatedAt()->format('Y-m-d H:i:s'),
                    $deliveryServiceRestrictPackage->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $deliveryServiceRestrictPackages));

        $table->render();

        $output->writeln(sprintf('Restrict package for delivery service %s successfully updated', $deliveryServiceCode));

        return Command::SUCCESS;
    }
}
