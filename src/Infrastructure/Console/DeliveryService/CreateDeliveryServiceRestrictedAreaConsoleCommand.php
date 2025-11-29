<?php

namespace App\Infrastructure\Console\DeliveryService;

use App\Application\CommandBus;
use App\Application\DeliveryService\Command\CreateDeliveryServiceRestrictAreaCommand;
use App\Application\DeliveryService\Query\FindDeliveryServiceRestrictAreaByDeliveryServiceIdQuery;
use App\Application\DeliveryService\Query\GetAllDeliveryServicesQuery;
use App\Application\QueryBus;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictArea;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictAreaRepositoryInterface;
use App\Domain\DeliveryService\ValueObject\Point;
use App\Domain\DeliveryService\ValueObject\Polygon;
use CrEOF\Geo\WKT\Parser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:delivery-service-restricted-area:create', description: 'Create delivery service restricted area console command')]
class CreateDeliveryServiceRestrictedAreaConsoleCommand extends Command
{
    public function __construct(
        public readonly CommandBus $commandBus,
        public readonly QueryBus $queryBus,
        public DeliveryServiceRestrictAreaRepositoryInterface $restrictAreaRepository
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
                return $deliveryService->getCode() . ' | ' . $deliveryService->getId();
            }, $deliveryServices),
            null
        );
        $choiceList->setErrorMessage("Selected delivery service %s is invalid.\n");
        $choiceAnswer = explode('|', $helper->ask($input, $output, $choiceList));
        $deliveryServiceId = Uuid::fromString(trim(end($choiceAnswer)));

        /**
         * Set name
         */
        $setNameQuestion = new Question("Set restricted area name \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 100) {
                throw new \RuntimeException(
                    'The name field is required and must be less than 100 characters'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set polygon
         */
        $setPolygonQuestion = new Question("Set restricted area polygon \n", null);
        $setPolygonQuestion->setValidator(function ($answer) {
            $coordinates = (new Parser($answer))->parse();

            $coordinates = array_map(function (array $coordinates) {
                return array_map(function (array $coordinate) {
                    return new Point($coordinate[1], $coordinate[0]);
                }, $coordinates);
            }, $coordinates['value']);

            return new Polygon($coordinates);
        });
        $polygon = $helper->ask($input, $output, $setPolygonQuestion);

        $this->commandBus->dispatch(new CreateDeliveryServiceRestrictAreaCommand($deliveryServiceId, $name, $polygon));

        $deliveryServiceRestrictedAreas = $this->queryBus->handle(new FindDeliveryServiceRestrictAreaByDeliveryServiceIdQuery(
            $deliveryServiceId
        ));

        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'name', 'is_active', 'created_at', 'updated_at'])
            ->setRows(array_map(function (DeliveryServiceRestrictArea $deliveryServiceRestrictedArea) {
                return [
                    $deliveryServiceRestrictedArea->getId()->toRfc4122(),
                    $deliveryServiceRestrictedArea->getName(),
                    $deliveryServiceRestrictedArea->isActive(),
                    $deliveryServiceRestrictedArea->getCreatedAt()->format('Y-m-d H:i:s'),
                    $deliveryServiceRestrictedArea->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $deliveryServiceRestrictedAreas));

        $table->render();

        $output->writeln('Delivery service restricted area successfully added');

        return Command::SUCCESS;
    }
}
