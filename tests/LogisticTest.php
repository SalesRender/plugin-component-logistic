<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;

use Leadvertex\Components\MoneyValue\MoneyValue;
use Leadvertex\Plugin\Components\Logistic\Exceptions\ShippingTimeException;
use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticDataTooBigException;
use Leadvertex\Plugin\Components\Logistic\Exceptions\NegativeLogisticPriceException;
use PHPUnit\Framework\TestCase;

class LogisticTest extends TestCase
{
    private array $data;

    private Logistic $logistic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'some data' => [
                'hello',
                'world'
            ],
        ];

        $this->logistic = new Logistic();
        $this->logistic->setData($this->data);
    }


    public function testGetData(): void
    {
        $this->assertSame($this->data, $this->logistic->getData());
    }

    public function testSetData(): void
    {
        $data = ['1', '2', 3];
        $this->logistic->setData($data);
        $this->assertSame($data, $this->logistic->getData());
    }

    public function testSetTooBigData(): void
    {
        $this->expectException(LogisticDataTooBigException::class);
        $data = [];
        for ($i = 1; $i <= 1000; $i++) {
            $data[md5(random_bytes(10))] = md5(random_bytes(10));
        }
        $this->logistic->setData($data);
    }

    public function testGetSetTrack(): void
    {
        $this->assertNull($this->logistic->getTrack());

        $track = new LogisticTrack('AB0_123-456-789cd');
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
        $delivery = new LogisticDelivery(LogisticDelivery::COURIER);
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
