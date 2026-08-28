<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic;

use InvalidArgumentException;
use SalesRender\Plugin\Components\Logistic\Exceptions\LogisticStatusTooLongException;
use PHPUnit\Framework\TestCase;

class LogisticStatusTest extends TestCase
{

    private LogisticStatus $status;

    private LogisticOffice $office;

    protected function setUp(): void
    {
        parent::setUp();
        $this->office = new LogisticOffice(null, ['+79887776655'], null);
        $this->status = new LogisticStatus(
            LogisticStatus::ACCEPTED,
            'Parcel accepted',
            1607955024,
            $this->office,
        );
    }

    public function testGetCode(): void
    {
        $this->assertSame(LogisticStatus::ACCEPTED, $this->status->getCode());
    }

    public function testGetText(): void
    {
        $this->assertSame('Parcel accepted', $this->status->getText());
    }

    public function testGetTimestamp(): void
    {
        $this->assertSame(1607955024, $this->status->getTimestamp());
    }

    public function testConstructWithoutTimestamp(): void
    {
        $status = new LogisticStatus(
            LogisticStatus::ACCEPTED,
            'Parcel accepted',
        );
        $this->assertTrue((time() - $status->getTimestamp()) < 2);
    }

    public function testConstructWithEmptyText(): void
    {
        $status = new LogisticStatus(LogisticStatus::ACCEPTED, '');
        $this->assertEmpty($status->getText());
    }

    public function testConstructWithEmptyWhitespaceText(): void
    {
        $status = new LogisticStatus(LogisticStatus::ACCEPTED, '   ');
        $this->assertEmpty($status->getText());
    }

    public function testConstructWithTooLongText(): void
    {
        $this->expectException(LogisticStatusTooLongException::class);
        new LogisticStatus(LogisticStatus::ACCEPTED, str_repeat('a', '251'));
    }

    public function testGetHash(): void
    {
        $hash = md5(json_encode([
            'timestamp' => $this->status->getTimestamp(),
            'code' => $this->status->getCode(),
            'text' => $this->status->getText(),
            'office' => $this->status->getOffice(),
        ]));
        $this->assertSame($hash, $this->status->getHash());
    }

    public function testGetHashNotAffectedByIndex(): void
    {
        $withIndex = $this->status->withIndex(1);
        $this->assertSame($this->status->getHash(), $withIndex->getHash());
    }

    public function testGetIndex(): void
    {
        $this->assertNull($this->status->getIndex());
    }

    public function testWithIndex(): void
    {
        $withIndex = $this->status->withIndex(4);

        $this->assertNotSame($this->status, $withIndex);
        $this->assertNull($this->status->getIndex());
        $this->assertSame(4, $withIndex->getIndex());
        $this->assertSame($this->status->getCode(), $withIndex->getCode());
        $this->assertSame($this->status->getTimestamp(), $withIndex->getTimestamp());
    }

    public function testWithIndexNotPositive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->status->withIndex(0);
    }

    public function testGetOffice(): void
    {
        $this->assertEquals($this->office, $this->status->getOffice());
    }

    public function testWithoutOffice(): void
    {
        $status = new LogisticStatus(
            LogisticStatus::ACCEPTED,
            'Parcel accepted',
            1607955024,
        );
        $this->assertNull($status->getOffice());
    }

    public function testJsonSerialize(): void
    {
        $expected = [
            'timestamp' => 1607955024,
            'code' => LogisticStatus::ACCEPTED,
            'text' => 'Parcel accepted',
            'office' => [
                'address' => null,
                'phones' => ['+79887776655'],
                'openingHours' => null,
            ],
            'index' => null,
        ];

        $this->assertSame(json_encode($expected), json_encode($this->status));
    }

    public function testJsonSerializeWithIndex(): void
    {
        $status = $this->status->withIndex(4);
        $this->assertSame(4, $status->jsonSerialize()['index']);
    }

}