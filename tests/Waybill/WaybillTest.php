<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic\Waybill;

use Leadvertex\Components\MoneyValue\MoneyValue;
use Leadvertex\Plugin\Components\Logistic\Exceptions\ShippingTimeException;
use Leadvertex\Plugin\Components\Logistic\Exceptions\NegativeLogisticPriceException;
use PHPUnit\Framework\TestCase;

class WaybillTest extends TestCase
{

    private Waybill $waybill;

    protected function setUp(): void
    {
        parent::setUp();
        $this->waybill = new Waybill();
    }

    public function testConstruct(): void
    {
        $track = new Track('AB0_123-456-789cd');
        $price = new MoneyValue(100500);
        $hours = 24;
        $delivery = new Delivery(Delivery::COURIER);
        $cod = true;

        $waybill = new Waybill($track, $price, $hours, $delivery, $cod);
        $this->assertSame($track, $waybill->getTrack());
        $this->assertSame($price, $waybill->getPrice());
        $this->assertSame($hours, $waybill->getShippingTime());
        $this->assertSame($delivery, $waybill->getDelivery());
        $this->assertTrue($waybill->isCod());

        $waybillNull = new Waybill();
        $this->assertNull($waybillNull->getTrack());
        $this->assertNull($waybillNull->getPrice());
        $this->assertNull($waybillNull->getShippingTime());
        $this->assertNull($waybillNull->getDelivery());
        $this->assertNull($waybillNull->isCod());
    }

    public function testConstructWithNegativePrice(): void
    {
        $this->expectException(NegativeLogisticPriceException::class);
        new Waybill(null, new MoneyValue(-100500));
    }

    /**
     * @dataProvider invalidShippingTImeDataProvider
     * @param $hours
     */
    public function testConstructWithInvalidShippingTime($hours): void
    {
        $this->expectException(ShippingTimeException::class);
        new Waybill(null, null, $hours);
    }

    public function testGetSetTrack(): void
    {
        $track = new Track('AB0_123-456-789cd');

        $this->assertNull($this->waybill->getTrack());
        $waybill = $this->waybill->setTrack($track);
        $this->assertNull($this->waybill->getTrack());
        $this->assertNotSame($waybill, $this->waybill);
        $this->assertSame($track, $waybill->getTrack());

        $waybillNull = $waybill->setTrack(null);
        $this->assertSame($track, $waybill->getTrack());
        $this->assertNull($waybillNull->getTrack());
        $this->assertNotSame($waybill, $waybillNull);
    }

    public function testGetSetPrice(): void
    {
        $price = new MoneyValue(100500);

        $this->assertNull($this->waybill->getPrice());
        $waybill = $this->waybill->setPrice($price);
        $this->assertNull($this->waybill->getPrice());
        $this->assertNotSame($waybill, $this->waybill);
        $this->assertSame($price, $waybill->getPrice());

        $waybillNull = $waybill->setPrice(null);
        $this->assertSame($price, $waybill->getPrice());
        $this->assertNull($waybillNull->getPrice());
        $this->assertNotSame($waybill, $waybillNull);
    }

    public function testSetPriceInvalid(): void
    {
        $this->expectException(NegativeLogisticPriceException::class);
        $this->waybill->setPrice(new MoneyValue(-100500));
    }

    public function testGetSetShippingTime(): void
    {
        $hours = 24;

        $this->assertNull($this->waybill->getShippingTime());
        $waybill = $this->waybill->setShippingTime($hours);
        $this->assertNull($this->waybill->getShippingTime());
        $this->assertNotSame($waybill, $this->waybill);
        $this->assertSame($hours, $waybill->getShippingTime());


        $waybillNull = $waybill->setShippingTime(null);
        $this->assertSame($hours, $waybill->getShippingTime());
        $this->assertNull($waybillNull->getShippingTime());
        $this->assertNotSame($waybill, $waybillNull);
    }

    /**
     * @dataProvider invalidShippingTImeDataProvider
     * @param int $hours
     * @throws ShippingTimeException
     */
    public function testSetShippingTimeInvalid(int $hours): void
    {
        $this->expectException(ShippingTimeException::class);
        $this->waybill->setShippingTime($hours);
    }

    public function testGetSetDelivery(): void
    {
        $delivery = new Delivery(Delivery::COURIER);

        $this->assertNull($this->waybill->getDelivery());
        $waybill = $this->waybill->setDelivery($delivery);
        $this->assertNull($this->waybill->getDelivery());
        $this->assertNotSame($waybill, $this->waybill);
        $this->assertSame($delivery, $waybill->getDelivery());

        $waybillNull = $waybill->setDelivery(null);
        $this->assertSame($delivery, $waybill->getDelivery());
        $this->assertNull($waybillNull->getDelivery());
        $this->assertNotSame($waybill, $waybillNull);
    }

    public function testGetSetCod(): void
    {
        $cod = true;

        $this->assertNull($this->waybill->isCod());
        $waybill = $this->waybill->setCod($cod);
        $this->assertNull($this->waybill->isCod());
        $this->assertNotSame($waybill, $this->waybill);
        $this->assertSame($cod, $waybill->isCod());

        $waybillNull = $waybill->setCod(null);
        $this->assertSame($cod, $waybill->isCod());
        $this->assertNull($waybillNull->isCod());
        $this->assertNotSame($waybill, $waybillNull);
    }

    public function invalidShippingTImeDataProvider(): array
    {
        return [
            [-1],
            [5001],
        ];
    }

}
