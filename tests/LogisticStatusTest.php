<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;

use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticStatusTooLongException;
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
        $hash = md5(json_encode($this->status->jsonSerialize()));
        $this->assertSame($hash, $this->status->getHash());
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
        ];

        $this->assertSame($expected, $this->status->jsonSerialize());

        $expected = [
            'timestamp' => 1607955024,
            'code' => LogisticStatus::ACCEPTED,
            'text' => 'Parcel accepted',
            'office' => null,
        ];
        $status = new LogisticStatus(
            LogisticStatus::ACCEPTED,
            'Parcel accepted',
            1607955024,
        );
        $this->assertSame($expected, $status->jsonSerialize());
    }

}