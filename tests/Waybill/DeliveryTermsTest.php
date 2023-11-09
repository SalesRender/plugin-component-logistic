<?php
/**
 * Created for plugin-component-logistic
 * Date: 19.01.2021
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic\Waybill;

use SalesRender\Plugin\Components\Logistic\Exceptions\DeliveryTermsException;
use PHPUnit\Framework\TestCase;

class DeliveryTermsTest extends TestCase
{

    private DeliveryTerms $terms;

    protected function setUp(): void
    {
        parent::setUp();
        $this->terms = new DeliveryTerms(1, 10);
    }

    public function testConstructWithNegativeMin(): void
    {
        $this->expectException(DeliveryTermsException::class);
        $this->expectExceptionCode(1);
        new DeliveryTerms(-1, 10);
    }

    public function testConstructWithTooBigMax(): void
    {
        $this->expectException(DeliveryTermsException::class);
        $this->expectExceptionCode(2);
        new DeliveryTerms(1, 365 * 24 + 1);
    }

    public function testConstructWithMinGreatThanMax(): void
    {
        $this->expectException(DeliveryTermsException::class);
        $this->expectExceptionCode(3);
        new DeliveryTerms(10, 1);
    }

    public function testGetMinAndMaxHours(): void
    {
        $this->assertSame(1, $this->terms->getMinHours());
        $this->assertSame(10, $this->terms->getMaxHours());
    }

    public function testJsonSerialize(): void
    {
        $this->assertSame('{"minHours":1,"maxHours":10}', json_encode($this->terms));
    }

}
