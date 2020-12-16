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

    private Waybill $logistic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logistic = new Waybill();
    }

    public function testGetSetTrack(): void
    {
        $this->assertNull($this->logistic->getTrack());

        $track = new Track('AB0_123-456-789cd');
        $this->logistic->setTrack($track);
        $this->assertSame($track, $this->logistic->getTrack());

        $this->logistic->setTrack(null);
        $this->assertNull($this->logistic->getTrack());
    }

    public function testGetSetPrice(): void
    {
        $this->assertNull($this->logistic->getPrice());
        $price = new MoneyValue(100500);
        $this->logistic->setPrice($price);
        $this->assertSame($price, $this->logistic->getPrice());
        $this->logistic->setPrice(null);
        $this->assertNull($this->logistic->getPrice());
    }

    public function testSetPriceInvalid(): void
    {
        $this->expectException(NegativeLogisticPriceException::class);
        $this->logistic->setPrice(new MoneyValue(-100500));
    }

    public function testGetSetShippingTime(): void
    {
        $this->assertNull($this->logistic->getShippingTime());
        $hours = 24;
        $this->logistic->setShippingTime($hours);
        $this->assertSame($hours, $this->logistic->getShippingTime());
        $this->logistic->setShippingTime(null);
        $this->assertNull($this->logistic->getShippingTime());
    }

    public function testSetShippingTimeInvalid(): void
    {
        $this->expectException(ShippingTimeException::class);
        $this->logistic->setShippingTime(5001);
    }

    public function testGetSetDelivery(): void
    {
        $this->assertNull($this->logistic->getDelivery());
        $delivery = new Delivery(Delivery::COURIER);
        $this->logistic->setDelivery($delivery);
        $this->assertSame($delivery, $this->logistic->getDelivery());
        $this->logistic->setDelivery(null);
        $this->assertNull($this->logistic->getDelivery());
    }

    public function testGetSetCod(): void
    {
        $this->assertNull($this->logistic->isCod());
        $this->logistic->setCod(true);
        $this->assertTrue($this->logistic->isCod());
        $this->logistic->setCod(false);
        $this->assertFalse($this->logistic->isCod());
        $this->logistic->setCod(null);
        $this->assertNull($this->logistic->isCod());
    }

}
