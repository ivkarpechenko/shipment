# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased] - yyyy-mm-dd

Here we write upgrading notes for shipment ms. It's a team effort to make them as
straightforward as possible.

## [1.0.0] - 2023-05-23

This version includes the implementation of the project architecture and the implementation of the main domains

### Added

#### Domains

- Country
- Region
- City
- Address
- Currency
- Contact
- DeliveryService
- TariffPlan
- Shipment

#### Console commands

- app:country:create
- app:country:update
- app:currency:create
- app:currency:update
- app:delivery-service:create
- app:delivery-service:update
- app:tariff-plan:create
- app:tariff-plan:update

## [1.1.0] - 2023-05-30

The release includes the implementation of the Calculate domain and the integration of the external CDEK service

### Added

#### Domains

- Calculate

#### Infrastructure

- CDEK
  - HTTP Auth service with cache
  - HTTP Calculate service

### Changed

#### Domains

- Country
- Shipment

#### Infrastructure

- ShipmentNormalizer

## [1.2.0] - 2023-06-07

This release includes the implementation of integration with "Деловые линии"

### Added

#### Console commands

- app:tariff-plan-update

#### Infrastructure

- Dellin
  - HTTP Auth service with cache
  - HTTP Calculate service

### Changed

#### Domains

- Shipment
  - psd -> date
  - psdStartTime -> time
  - psdEndTime -> time
- Package
  - price -> decimal(12, 2)

## [1.2.1] - 2023-06-14

The release includes minor changes

#### Infrastructure

- Shipment HTTP Response
  - psdStartTime
  - psdEndTime

### Changed

#### Domains

- Address
  - house -> required

## [1.3.0] - 2023-06-16

The release includes minor changes

### Added

#### Domains

- Tax

#### Console commands

- app:tax:create

### Changed

#### Domains

- Calculate
  - deliveryTotalCostTax -> required

## [1.3.1] - 2023-06-19

The release includes minor changes

### Changed

#### Infrastructure

- Dadata

## [1.3.2] - 2023-06-22

The release includes minor changes

### Changed

#### Domains

- Address

### Changed

#### Infrastructure

- CDEK
- Dellin

## [1.3.3] - 2023-06-27

The release includes minor changes

### Changed

#### Infrastructure

- Dellin
  - DellinShipmentDtoNormalizer

## [1.3.4] - 2023-06-27

The release includes minor changes

### Changed

#### Domain

- Shipment
  - CreateShipmentService

#### Infrastructure

- Dellin
  - DellinCalculateDtoDenormalizer
- CDEK
  - CdekShipmentDtoNormalizer
  - CdekCalculateStrategy

## [1.4.0] - 2023-07-03

The release implements integration with the logistics operator Dostavista and fixes bugs "CDEK" and "Dellin"

### Added

#### Domains

- Package -> Product

#### Infrastructure

- DeliveryService
  - Service -> DostavistaCalculateService
  - Strategy -> DostavistaCalculateStrategy
  - Strategy -> DostavistaTariffPlanStrategy

### Changed

#### Infrastructure

- Dellin
  - DellinShipmentDtoNormalizer
- CDEK
  - CdekShipmentDtoNormalizer

## [1.4.1] - 2023-07-11

The release includes minor changes

### Changed

#### Infrastructure

- CDEK
  - Strategy -> CdekCalculateStrategy

## [1.4.2] - 2023-07-12

The release includes minor changes

### Changed

#### Infrastructure

- CDEK
  - Service -> Response -> Denormalizer -> CdekCalculateDtoDenormalizer
- Dellin
  - Service -> Request -> Normalizer -> DellinShipmentDtoNormalizer

## [1.4.3] - 2023-07-21

The release includes minor changes

### Changed

#### Infrastructure

- CDEK
  - Strategy -> CdekCalculateStrategy
- Dellin
  - Strategy -> CdekCalculateStrategy
- Http
  - JSONExceptionListener

## [1.4.4] - 2023-07-24

The release includes minor changes

### Changed

#### Domain

- Address
  - Entity -> Address

#### Infrastructure

- Http
  - JSONExceptionListener

## [1.4.5] - 2023-07-27

The release includes minor changes

### Added

#### Infrastructure

- ExceptionListener

## Removed

#### Infrastructure

- Http
  - JSONExceptionListener

## [1.4.6] - 2023-09-04

The release includes minor changes

### Added

#### Infrastructure

- DeliveryService -> Dostavista -> Enum -> DostavistaEnum
- DeliveryService -> Dostavista -> Enum -> DostavistaVehicleTypeEnum

## Changed

#### Infrastructure

- DeliveryService -> Dostavista -> Service -> DostavistaCalculateService
- DeliveryService -> Dostavista -> Service -> Request -> Normalizer -> DostavistaShipmentDtoNormalizer
- DeliveryService -> Dostavista -> Service -> Response -> Denormalizer -> DostavistaCalculateDtoDenormalizer
- DeliveryService -> Dostavista -> Strategy -> DostavistaTariffPlanStrategy

## [1.4.7] - 2023-09-12

Hotfix

## [1.4.8] - 2023-09-19

Added support for the dadata clean address module

### Added

#### Domain

- Address -> Entity -> Address -> Settlement

#### Infrastructure

- Console -> Address -> ExternalUpdateAddressConsoleCommand
- DaData -> Response -> Denormalizer -> DaDataCleanAddressDtoDenormalizer
- DaData -> Service -> FindBySuggestAddressService

## Changed

#### Infrastructure

- DaData -> Response -> (DaDataAddressDtoDenormalizer -> DaDataSuggestAddressDtoDenormalizer)
- DaData -> Service -> (FindByAddressService -> FindByCleanAddressService)
- DeliveryService -> Dellin -> Service -> Request -> Normalizer -> DellinShipmentDtoNormalizer

## [1.4.9] - 2023-09-19

Hotfix

## [1.5.0] - 2023-09-28

Added Http client logging

## [1.5.1] - 2023-10-04

Added foot delivery vehicle type

## [1.5.2] - 2023-10-17

Hotfix ($city) must be of type string, null given

## [1.5.4] - 2023-11-21

Hotfix Dellin

## [1.5.5] - 2023-11-22

Hotfix Dellin

## [1.5.6] - 2024-01-12

Dostavista:
min_period = PSD + start_datetime
max_period = min_period + 1

## [1.6.0] - 2024-03-11

### Added

#### Domains

- Added new fields (point, input_data) to Address domain

### Changed

#### Infrastructure

Dostavista:

- Refactoring calculate params

Dellin:

- Refactoring from and to address params

## [1.6.1] - 2024-03-11

### Changed

#### Infrastructure

Dellin:

- Refactoring from and to address params

## [1.6.2] - 2024-03-18

### Changed

#### Infrastructure

Dostavista:

- Added new warning param invalid_region

## [1.6.3] - 2024-03-18

### Changed

#### Infrastructure

Dostavista:

- Refactoring find warning parameters

## [1.7.0] - 2024-04-15

### Added

#### Domain

- Domain -> DeliveryService -> Entity -> DeliveryServiceRestrictArea
- Domain -> DeliveryService -> ValueObject -> Polygon
- Domain -> Shipment -> Service -> CheckAddressInRestrictedAreaService

#### Infrastructure

- DBAL -> Function -> PostgreSQL -> STAsGeoJSON
- DBAL -> Function -> PostgreSQL -> STAsText
- DBAL -> Function -> PostgreSQL -> STContains
- DBAL -> Function -> PostgreSQL -> STGeomFromText
- DBAL -> Types -> Doctrine -> PolygonType

### Changed

- Domain -> Shipment -> Service -> CreateShipmentService

### [1.39] (2024-10-24)

### Features

- RD-6398 Добавлено ограничение упаковки для логистических операторов

### [1.40] (2024-11-14)

### Features

- RD-6474 Ограничение расчета доставки в ЛО

### [1.44] (2025-01-28)

### Features

- RD-6300 Добавлены методы доставки
- RD-6301 Изменение создания отправлений
- RD-6302 Получение информации о ПВЗ СДЕК
- RD-6870 Рефакторинг получения ПВЗ СДЕК
- RD-6944 Ограничения пунктов выдачи
- RD-6947 Выбор Пункта на карте

### Bug Fixes

- RD-6909 Исправление получения расчета

### Chore

- RD-6822 Настроен phpstan

### [1.45] (2025-02-14)

### Features

- RD-6697 Загрузить справочник ОКАТО-ОКТМО
- RD-6698 Получение адреса по ОКТМО из Dadata
- RD-6702 Новый роут shipment-ms
- RD-6890 Переделать отправления

### [1.47] (2025-02-27)

### Features

- RD-7112 Загрузка пунктов выдачи Деловые линии
- RD-7120 Расчет стоимости доставки до ПВЗ Деловые линии
