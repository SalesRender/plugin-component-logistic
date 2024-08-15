<?php
/**
 * Created for plugin-component-logistic
 * Date: 16.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic\Waybill;

use PHPUnit\Framework\TestCase;
use XAKEPEHOK\EnumHelper\Exception\OutOfEnumException;

class DeliveryTypeTest extends TestCase
{

    private DeliveryType $delivery;

    protected function setUp(): void
    {
        parent::setUp();
        $this->delivery = new DeliveryType(DeliveryType::PICKUP_POINT);
    }

    public function testConstructInvalidValue(): void
    {
        $this->expectException(OutOfEnumException::class);
        new DeliveryType(100500);
    }

    public function testGet(): void
    {
        $this->assertSame(DeliveryType::PICKUP_POINT, $this->delivery->get());
    }

    public function testGetAsString(): void
    {
        $this->assertSame('PICKUP_POINT', (new DeliveryType(DeliveryType::PICKUP_POINT))->getAsString());
        $this->assertSame('SELF_PICKUP', (new DeliveryType(DeliveryType::SELF_PICKUP))->getAsString());
        $this->assertSame('COURIER', (new DeliveryType(DeliveryType::COURIER))->getAsString());
    }

    public function testGetValues(): void
    {
        $this->assertSame(['COURIER', 'PICKUP_POINT', 'SELF_PICKUP'], DeliveryType::values());
    }

    public function testJsonSerialize()
    {
        $this->assertSame('"PICKUP_POINT"', json_encode($this->delivery));
    }
}
