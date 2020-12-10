<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;

use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticStatusTooLongException;
use PHPUnit\Framework\TestCase;

class LogisticTrackStatusTest extends TestCase
{

    private LogisticTrackStatus $status;

    protected function setUp(): void
    {
        parent::setUp();
        $this->status = new LogisticTrackStatus(LogisticTrackStatus::ACCEPTED, 'Parcel accepted');
    }

    public function testGetCode()
    {
        $this->assertSame(LogisticTrackStatus::ACCEPTED, $this->status->getCode());
    }

    public function testGetText()
    {
        $this->assertSame('Parcel accepted', $this->status->getText());
    }

    public function testConstructWithNullText()
    {
        $status = new LogisticTrackStatus(LogisticTrackStatus::ACCEPTED, null);
        $this->assertNull($status->getText());
    }

    public function testConstructWithEmptyText()
    {
        $status = new LogisticTrackStatus(LogisticTrackStatus::ACCEPTED, '');
        $this->assertNull($status->getText());
    }

    public function testConstructWithEmptyWhitespaceText()
    {
        $status = new LogisticTrackStatus(LogisticTrackStatus::ACCEPTED, '   ');
        $this->assertNull($status->getText());
    }

    public function testConstructWithTooLongText()
    {
        $this->expectException(LogisticStatusTooLongException::class);
        new LogisticTrackStatus(LogisticTrackStatus::ACCEPTED, str_repeat('a', '251'));
    }

}