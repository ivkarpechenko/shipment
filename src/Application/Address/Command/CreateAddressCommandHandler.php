<?php

namespace App\Application\Address\Command;

use App\Application\Address\Query\External\FindExternalAddressQuery;
use App\Application\City\Command\CreateCityCommand;
use App\Application\City\Query\FindCityByTypeAndNameQuery;
use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\Country\Query\FindCountryByCodeQuery;
use App\Application\QueryBus;
use App\Application\Region\Command\CreateRegionCommand;
use App\Application\Region\Query\FindRegionByCodeQuery;
use App\Domain\Address\Exception\AddressNotFoundException;
use App\Domain\Address\Service\CreateAddressService;
use App\Domain\Country\Exception\CountryNotAllowedException;

readonly class CreateAddressCommandHandler implements CommandHandler
{
    private const NEW_REGIONS_ISO_CODES =
        [
            'd28a09a6-ad4e-407a-ad64-c208549befc4' => 'RU-DON',
            '7bac30d2-0e95-499e-a34a-7351a8f13833' => 'RU-ZAP',
            '10530e14-2627-4e68-aae4-c57373defcf4' => 'RU-LUG',
            '486c7c54-caa1-4b04-8dd3-c182313337fc' => 'RU-HER',
        ];

    public function __construct(
        public CreateAddressService $createAddressService,
        public CommandBus $commandBus,
        public QueryBus $queryBus
    ) {
    }

    public function __invoke(CreateAddressCommand $command): void
    {
        $dto = $this->queryBus->handle(new FindExternalAddressQuery($command->getAddress()));
        if (is_null($dto)) {
            throw new AddressNotFoundException(sprintf(
                'The external source did not find the transmitted address %s',
                $command->getAddress()
            ));
        }

        $country = $this->queryBus->handle(new FindCountryByCodeQuery($dto->countryIsoCode));
        if (is_null($country)) {
            throw new CountryNotAllowedException(sprintf('Country %s not allowed', $dto->country));
        }

        if (!is_null($dto->regionIsoCode)) {
            $region = $this->queryBus->handle(new FindRegionByCodeQuery($dto->regionIsoCode));
            if (is_null($region)) {
                $this->commandBus->dispatch(new CreateRegionCommand($dto->countryIsoCode, $dto->region, $dto->regionIsoCode));
            }

            $city = $this->queryBus->handle(new FindCityByTypeAndNameQuery($dto->cityType, $dto->city));
            if (is_null($city)) {
                $this->commandBus->dispatch(new CreateCityCommand($dto->regionIsoCode, $dto->cityType, $dto->city));
            }
        } else {
            $newCode = self::NEW_REGIONS_ISO_CODES[$dto->inputData['data']['region_fias_id']] ?? null;

            if (!is_null($newCode)) {
                $region = $this->queryBus->handle(new FindRegionByCodeQuery($newCode));
                if (is_null($region)) {
                    $this->commandBus->dispatch(new CreateRegionCommand($dto->countryIsoCode, $dto->region, $newCode));
                }

                $city = $this->queryBus->handle(new FindCityByTypeAndNameQuery($dto->cityType, $dto->city));
                if (is_null($city)) {
                    $this->commandBus->dispatch(new CreateCityCommand($newCode, $dto->cityType, $dto->city));
                }
            }
        }

        $this->createAddressService->create($dto);
    }
}
