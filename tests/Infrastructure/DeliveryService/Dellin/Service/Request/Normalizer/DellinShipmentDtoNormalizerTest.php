<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\DeliveryService\Dellin\Service\Request\Normalizer;

use App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto\DellinShipmentDto;
use App\Infrastructure\DeliveryService\Dellin\Service\Request\Normalizer\DellinShipmentDtoNormalizer;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\PickupPoint\PickupPointFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DellinShipmentDtoNormalizerTest extends KernelTestCase
{
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->getContainer()->get(LoggerInterface::class);
    }

    public function testNormalizeWithoutPickupPoint(): void
    {
        $tariffPlan = TariffPlanFixture::getOneFilled();
        $dto = DellinShipmentDto::from(
            shipment: ShipmentFixture::getOneFilled(),
            tariffPlan: $tariffPlan
        );

        $normalizer = new DellinShipmentDtoNormalizer();

        $data = $normalizer->normalize($dto);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('delivery', $data);
        $this->assertArrayHasKey('cargo', $data);
        $this->assertArrayHasKey('hazardClass', $data);
        $this->assertArrayHasKey('insurance', $data);
        $this->assertArrayHasKey('members', $data);
        $this->assertArrayHasKey('payment', $data);

        $delivery = $data['delivery'];
        $this->assertIsArray($delivery);
        $this->assertArrayHasKey('deliveryType', $delivery);
        $this->assertArrayHasKey('derival', $delivery);
        $this->assertArrayHasKey('arrival', $delivery);

        $deliveryType = $delivery['deliveryType'];
        $this->assertIsArray($deliveryType);
        $this->assertArrayHasKey('type', $deliveryType);
        $this->assertEquals($tariffPlan->getCode(), $deliveryType['type']);

        $derival = $delivery['derival'];
        $this->assertIsArray($derival);
        $this->assertArrayHasKey('produceDate', $derival);
        $this->assertArrayHasKey('variant', $derival);
        $this->assertArrayHasKey('address', $derival);
        $this->assertArrayHasKey('time', $derival);

        $produceDate = $derival['produceDate'];
        $this->assertIsString($produceDate);
        $this->assertEquals($dto->psd->format('Y-m-d'), $produceDate);

        $this->assertEquals('address', $derival['variant']);

        $address = $derival['address'];
        $this->assertIsArray($address);
        $this->assertArrayHasKey('search', $address);
        $this->assertEquals($dto->from->getInputDataBy('value')->current(), $address['search']);

        $time = $derival['time'];
        $this->assertIsArray($time);
        $this->assertArrayHasKey('worktimeStart', $time);
        $this->assertArrayHasKey('worktimeEnd', $time);
        $this->assertEquals($dto->psdStartTime->format('G:i'), $time['worktimeStart']);
        $this->assertEquals($dto->psdEndTime->format('G:i'), $time['worktimeStart']);

        $arrival = $delivery['arrival'];
        $this->assertIsArray($arrival);
        $this->assertArrayHasKey('variant', $arrival);
        $this->assertArrayHasKey('address', $arrival);
        $this->assertArrayHasKey('time', $arrival);

        $this->assertEquals('address', $arrival['variant']);

        $this->assertIsArray($arrival['address']);
        $this->assertArrayHasKey('search', $arrival['address']);
        $this->assertEquals($dto->to->getInputDataBy('value')->current(), $arrival['address']['search']);

        $this->assertIsArray($arrival['time']);
        $this->assertArrayHasKey('worktimeStart', $arrival['time']);
        $this->assertArrayHasKey('worktimeEnd', $arrival['time']);

        $this->assertEquals('09:00', $arrival['time']['worktimeStart']);
        $this->assertEquals('18:00', $arrival['time']['worktimeEnd']);

        $cargo = $data['cargo'];
        $this->assertIsArray($cargo);
        $this->assertArrayHasKey('quantity', $cargo);
        $this->assertArrayHasKey('length', $cargo);
        $this->assertArrayHasKey('width', $cargo);
        $this->assertArrayHasKey('height', $cargo);
        $this->assertArrayHasKey('weight', $cargo);
        $this->assertArrayHasKey('totalVolume', $cargo);
        $this->assertArrayHasKey('totalWeight', $cargo);
        $this->assertArrayHasKey('oversizedWeight', $cargo);
        $this->assertArrayHasKey('oversizedVolume', $cargo);
        $this->assertArrayHasKey('freightUID', $cargo);

        $this->assertEquals($cargo['quantity'], $dto->packages->count());
        $this->assertEquals($cargo['length'], 1 / 1000);
        $this->assertEquals($cargo['width'], 1 / 1000);
        $this->assertEquals($cargo['height'], 1 / 1000);
        $this->assertEquals($cargo['weight'], 1 / 1000);
        $this->assertEquals($cargo['totalVolume'], 1 / (1000 * 1000 * 1000));
        $this->assertEquals($cargo['totalWeight'], 1 / 1000);
        $this->assertEquals($cargo['oversizedWeight'], 1 / 1000);
        $this->assertEquals($cargo['oversizedVolume'], 1 / (1000 * 1000 * 1000));
        $this->assertEquals($cargo['freightUID'], '0xbfcaad5766424ecd4eb5b4ede1e6bc97');

        $this->assertEquals(0, $data['hazardClass']);

        $insurance = $data['insurance'];
        $this->assertIsArray($insurance);
        $this->assertArrayHasKey('statedValue', $insurance);
        $this->assertArrayHasKey('term', $insurance);
        $this->assertEquals(1, $insurance['statedValue']);
        $this->assertTrue($insurance['term']);

        $members = $data['members'];
        $this->assertIsArray($members);
        $this->assertArrayHasKey('requester', $members);
        $this->assertIsArray($members['requester']);
        $this->assertArrayHasKey('role', $members['requester']);
        $this->assertEquals('third', $members['requester']['role']);

        $payment = $data['payment'];
        $this->assertIsArray($payment);
        $this->assertArrayHasKey('type', $payment);
        $this->assertArrayHasKey('paymentCity', $payment);
        $this->assertEquals('noncash', $payment['type']);
        $this->assertEquals('7700000000000000000000000', $payment['paymentCity']);
    }

    public function testNormalizeWithPickupPoint(): void
    {
        $shipment = ShipmentFixture::getOneFilled();
        $shipment->changePickupPoint(
            PickupPointFixture::getOne(
                deliveryService: DeliveryServiceFixture::getOne('test', 'test')
            )
        );
        $tariffPlan = TariffPlanFixture::getOneFilled();
        $dto = DellinShipmentDto::from(
            shipment: $shipment,
            tariffPlan: $tariffPlan
        );

        $normalizer = new DellinShipmentDtoNormalizer();

        $data = $normalizer->normalize($dto);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('delivery', $data);
        $this->assertArrayHasKey('cargo', $data);
        $this->assertArrayHasKey('hazardClass', $data);
        $this->assertArrayHasKey('insurance', $data);
        $this->assertArrayHasKey('members', $data);
        $this->assertArrayHasKey('payment', $data);

        $delivery = $data['delivery'];
        $this->assertIsArray($delivery);
        $this->assertArrayHasKey('deliveryType', $delivery);
        $this->assertArrayHasKey('derival', $delivery);
        $this->assertArrayHasKey('arrival', $delivery);

        $deliveryType = $delivery['deliveryType'];
        $this->assertIsArray($deliveryType);
        $this->assertArrayHasKey('type', $deliveryType);
        $this->assertEquals($tariffPlan->getCode(), $deliveryType['type']);

        $derival = $delivery['derival'];
        $this->assertIsArray($derival);
        $this->assertArrayHasKey('produceDate', $derival);
        $this->assertArrayHasKey('variant', $derival);
        $this->assertArrayHasKey('address', $derival);
        $this->assertArrayHasKey('time', $derival);

        $produceDate = $derival['produceDate'];
        $this->assertIsString($produceDate);
        $this->assertEquals($dto->psd->format('Y-m-d'), $produceDate);

        $this->assertEquals('address', $derival['variant']);

        $address = $derival['address'];
        $this->assertIsArray($address);
        $this->assertArrayHasKey('search', $address);
        $this->assertEquals($dto->from->getInputDataBy('value')->current(), $address['search']);

        $time = $derival['time'];
        $this->assertIsArray($time);
        $this->assertArrayHasKey('worktimeStart', $time);
        $this->assertArrayHasKey('worktimeEnd', $time);
        $this->assertEquals($dto->psdStartTime->format('G:i'), $time['worktimeStart']);
        $this->assertEquals($dto->psdEndTime->format('G:i'), $time['worktimeStart']);

        $arrival = $delivery['arrival'];
        $this->assertIsArray($arrival);
        $this->assertArrayHasKey('variant', $arrival);
        $this->assertArrayHasKey('terminalID', $arrival);
        $this->assertArrayHasKey('time', $arrival);

        $this->assertEquals('terminal', $arrival['variant']);

        $this->assertEquals($dto->pickupPoint->getCode(), $arrival['terminalID']);

        $this->assertIsArray($arrival['time']);
        $this->assertArrayHasKey('worktimeStart', $arrival['time']);
        $this->assertArrayHasKey('worktimeEnd', $arrival['time']);

        $this->assertEquals('09:00', $arrival['time']['worktimeStart']);
        $this->assertEquals('18:00', $arrival['time']['worktimeEnd']);

        $cargo = $data['cargo'];
        $this->assertIsArray($cargo);
        $this->assertArrayHasKey('quantity', $cargo);
        $this->assertArrayHasKey('length', $cargo);
        $this->assertArrayHasKey('width', $cargo);
        $this->assertArrayHasKey('height', $cargo);
        $this->assertArrayHasKey('weight', $cargo);
        $this->assertArrayHasKey('totalVolume', $cargo);
        $this->assertArrayHasKey('totalWeight', $cargo);
        $this->assertArrayHasKey('oversizedWeight', $cargo);
        $this->assertArrayHasKey('oversizedVolume', $cargo);
        $this->assertArrayHasKey('freightUID', $cargo);

        $this->assertEquals($cargo['quantity'], $dto->packages->count());
        $this->assertEquals($cargo['length'], 1 / 1000);
        $this->assertEquals($cargo['width'], 1 / 1000);
        $this->assertEquals($cargo['height'], 1 / 1000);
        $this->assertEquals($cargo['weight'], 1 / 1000);
        $this->assertEquals($cargo['totalVolume'], 1 / (1000 * 1000 * 1000));
        $this->assertEquals($cargo['totalWeight'], 1 / 1000);
        $this->assertEquals($cargo['oversizedWeight'], 1 / 1000);
        $this->assertEquals($cargo['oversizedVolume'], 1 / (1000 * 1000 * 1000));
        $this->assertEquals($cargo['freightUID'], '0xbfcaad5766424ecd4eb5b4ede1e6bc97');

        $this->assertEquals(0, $data['hazardClass']);

        $insurance = $data['insurance'];
        $this->assertIsArray($insurance);
        $this->assertArrayHasKey('statedValue', $insurance);
        $this->assertArrayHasKey('term', $insurance);
        $this->assertEquals(1, $insurance['statedValue']);
        $this->assertTrue($insurance['term']);

        $members = $data['members'];
        $this->assertIsArray($members);
        $this->assertArrayHasKey('requester', $members);
        $this->assertIsArray($members['requester']);
        $this->assertArrayHasKey('role', $members['requester']);
        $this->assertEquals('third', $members['requester']['role']);

        $payment = $data['payment'];
        $this->assertIsArray($payment);
        $this->assertArrayHasKey('type', $payment);
        $this->assertArrayHasKey('paymentCity', $payment);
        $this->assertEquals('noncash', $payment['type']);
        $this->assertEquals('7700000000000000000000000', $payment['paymentCity']);
    }
}
