<?php

namespace App\Infrastructure\Console\Address;

use App\Application\Address\Query\External\FindExternalAddressQuery;
use App\Application\Address\Query\GetAllAddressesQuery;
use App\Application\CommandBus;
use App\Application\QueryBus;
use App\Domain\Address\Entity\Address;
use App\Domain\Address\Service\Dto\AddressDto;
use App\Domain\Address\ValueObject\Point;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated
 */
#[AsCommand(name: 'app:address:external-update', description: 'External update addresses')]
class ExternalUpdateAddressConsoleCommand extends Command
{
    public function __construct(
        public readonly CommandBus $commandBus,
        public readonly QueryBus $queryBus,
        public readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $addresses = $this->queryBus->handle(new GetAllAddressesQuery());

        $progressBar = new ProgressBar($output, count($addresses));
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();

        $requestCount = 1;

        /** @var Address $address */
        foreach ($addresses as $address) {
            try {
                /** @var AddressDto $addressDto */
                $addressDto = $this->queryBus->handle(new FindExternalAddressQuery($address->getAddress()));
                if (is_null($addressDto)) {
                    continue;
                }
            } catch (\Exception $exception) {
                $output->writeln($exception->getMessage());

                continue;
            }

            if (!is_null($addressDto->street)) {
                $address->changeStreet($addressDto->street);
            }

            if (!is_null($addressDto->house)) {
                $address->changeHouse($addressDto->house);
            }

            if (!is_null($addressDto->latitude) && !is_null($addressDto->longitude)) {
                $address->changePoint(new Point($addressDto->latitude, $addressDto->longitude));
            }

            $address->changePostalCode($addressDto->postalCode);
            $address->changeFlat($addressDto->flat);
            $address->changeEntrance($addressDto->entrance);
            $address->changeFloor($addressDto->floor);
            $address->changeSettlement($addressDto->settlement);
            $address->changeInputData($addressDto->inputData);

            $this->entityManager->persist($address);

            $progressBar->advance();

            // Rate limit DaData
            ++$requestCount;
            if ($requestCount === 30) {
                sleep(1);
                $requestCount = 0;
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        echo PHP_EOL;
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
