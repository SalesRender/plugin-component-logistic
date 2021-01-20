<?php
/**
 * Created for plugin-component-logistic
 * Date: 16.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic\Waybill;

use PHPUnit\Framework\TestCase;
use XAKEPEHOK\EnumHelper\Exception\OutOfEnumException;

class DeliveryTest extends TestCase
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

    public function testGetValues(): void
    {
        $this->assertSame([300, 200, 100], DeliveryType::values());
    }

    public function testJsonSerialize()
    {
        $this->assertSame('200', json_encode($this->delivery));
    }
}
