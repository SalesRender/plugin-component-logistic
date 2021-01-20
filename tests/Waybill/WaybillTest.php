<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic\Waybill;

use Leadvertex\Components\MoneyValue\MoneyValue;
use Leadvertex\Plugin\Components\Logistic\Exceptions\NegativePriceException;
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
        $deliveryTerms = new DeliveryTerms(1, 24);
        $deliveryType = new DeliveryType(DeliveryType::COURIER);
        $cod = true;

        $waybill = new Waybill($track, $price, $deliveryTerms, $deliveryType, $cod);
        $this->assertSame($track, $waybill->getTrack());
        $this->assertSame($price, $waybill->getPrice());
        $this->assertSame($deliveryTerms, $waybill->getDeliveryTerms());
        $this->assertSame($deliveryType, $waybill->getDeliveryType());
        $this->assertTrue($waybill->isCod());

        $waybillNull = new Waybill();
        $this->assertNull($waybillNull->getTrack());
        $this->assertNull($waybillNull->getPrice());
        $this->assertNull($waybillNull->getDeliveryTerms());
        $this->assertNull($waybillNull->getDeliveryType());
        $this->assertNull($waybillNull->isCod());
    }

    public function testConstructWithNegativePrice(): void
    {
        $this->expectException(NegativePriceException::class);
        new Waybill(null, new MoneyValue(-100500));
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
        $this->expectException(NegativePriceException::class);
        $this->waybill->setPrice(new MoneyValue(-100500));
    }

    public function testGetSetDeliveryTerms(): void
    {
        $terms = new DeliveryTerms(1, 10);

        $this->assertNull($this->waybill->getDeliveryTerms());
        $waybill = $this->waybill->setDeliveryTerms($terms);
        $this->assertNull($this->waybill->getDeliveryTerms());
        $this->assertNotSame($waybill, $this->waybill);
        $this->assertSame($terms, $waybill->getDeliveryTerms());


        $waybillNull = $waybill->setDeliveryTerms(null);
        $this->assertSame($terms, $waybill->getDeliveryTerms());
        $this->assertNull($waybillNull->getDeliveryTerms());
        $this->assertNotSame($waybill, $waybillNull);
    }

    public function testGetSetDeliveryType(): void
    {
        $delivery = new DeliveryType(DeliveryType::COURIER);

        $this->assertNull($this->waybill->getDeliveryType());
        $waybill = $this->waybill->setDeliveryType($delivery);
        $this->assertNull($this->waybill->getDeliveryType());
        $this->assertNotSame($waybill, $this->waybill);
        $this->assertSame($delivery, $waybill->getDeliveryType());

        $waybillNull = $waybill->setDeliveryType(null);
        $this->assertSame($delivery, $waybill->getDeliveryType());
        $this->assertNull($waybillNull->getDeliveryType());
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

   public function testCreateFromArray(): void
    {
        $waybill = Waybill::createFromArray([
            'track' => 'AB012345789CD',
            'price' => 100500,
            'deliveryTerms' => [
                'minHours' => 1,
                'maxHours' => 10,
            ],
            'deliveryType' => DeliveryType::COURIER,
            'cod' => true,
        ]);

        $this->assertInstanceOf(Waybill::class, $waybill);

        $this->assertInstanceOf(Track::class, $waybill->getTrack());
        $this->assertEquals('AB012345789CD', $waybill->getTrack()->get());

        $this->assertInstanceOf(MoneyValue::class, $waybill->getPrice());
        $this->assertEquals(100500, $waybill->getPrice()->getAmount());

        $this->assertInstanceOf(DeliveryTerms::class, $waybill->getDeliveryTerms());
        $this->assertEquals(1, $waybill->getDeliveryTerms()->getMinHours());
        $this->assertEquals(10, $waybill->getDeliveryTerms()->getMaxHours());

        $this->assertInstanceOf(DeliveryType::class, $waybill->getDeliveryType());
        $this->assertEquals(DeliveryType::COURIER, $waybill->getDeliveryType()->get());

        $this->assertSame(true, $waybill->isCod());

        ###########

        $waybill = Waybill::createFromArray([
            'track' => null,
            'price' => null,
            'deliveryTerms' => null,
            'deliveryType' => null,
            'cod' => null,
        ]);

        $this->assertInstanceOf(Waybill::class, $waybill);
        $this->assertNull($waybill->getTrack());
        $this->assertNull($waybill->getPrice());
        $this->assertNull($waybill->getDeliveryTerms());
        $this->assertNull($waybill->getDeliveryType());
        $this->assertNull($waybill->isCod());
    }

}
