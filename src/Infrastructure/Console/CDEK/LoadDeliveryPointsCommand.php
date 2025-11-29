<?php

namespace App\Infrastructure\Console\CDEK;

use App\Application\CommandBus;
use App\Application\PickupPoint\Command\CreateCdekPickupPointCommand;
use App\Application\QueryBus;
use App\Infrastructure\DeliveryService\CDEK\Service\CdekPickupPointService;
use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekPickupPointDto;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:cdek:deliverypoints', description: 'Загрузка пунктов самовывоза СДЕК')]
class LoadDeliveryPointsCommand extends Command
{
    public function __construct(
        public readonly CdekPickupPointService $cdekPickupPointService,
        public readonly LoggerInterface $logger,
        public readonly CommandBus $commandBus,
        public readonly QueryBus $queryBus
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var CdekPickupPointDto[] $pickupPoints */
        $pickupPoints = $this->cdekPickupPointService->deliveryPoints();

        $progressBar = new ProgressBar($output, count($pickupPoints));
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();

        $this->logger->error('LoadDeliveryPointsCommand for address ');
        foreach ($pickupPoints as $pickupPoint) {
            try {
                $this->commandBus->dispatch(new CreateCdekPickupPointCommand($pickupPoint));
                $progressBar->advance();
            } catch (\Throwable $exception) {
                $this->logger->error('LoadDeliveryPointsCommand for address ' . $pickupPoint->address . '. Error: ' . $exception->getMessage());
                $progressBar->advance();
            }
        }

        echo PHP_EOL;
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
