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

    private Delivery $delivery;

    protected function setUp(): void
    {
        parent::setUp();
        $this->delivery = new Delivery(Delivery::PICKUP_POINT);
    }

    public function testConstructInvalidValue(): void
    {
        $this->expectException(OutOfEnumException::class);
        new Delivery(100500);
    }

    public function testGet(): void
    {
        $this->assertSame(Delivery::PICKUP_POINT, $this->delivery->get());
    }

    public function testGetValues(): void
    {
        $this->assertSame([300, 200, 100], Delivery::values());
    }

    public function testJsonSerialize()
    {
        $this->assertSame('200', json_encode($this->delivery));
    }
}
