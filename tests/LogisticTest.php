<?php
/**
 * Created for plugin-component-logistic
 * Date: 14.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic;

use SalesRender\Plugin\Components\Logistic\Exceptions\LogisticDataTooBigException;
use SalesRender\Plugin\Components\Logistic\Exceptions\LogisticTagTooLongException;
use SalesRender\Plugin\Components\Logistic\Waybill\Waybill;
use PHPUnit\Framework\TestCase;

class LogisticTest extends TestCase
{

    private Waybill $info;
    private LogisticStatus $status;
    private array $data;
    private Logistic $logistic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->info = $this->createMock(Waybill::class);
        $this->status = $this->createMock(LogisticStatus::class);
        $this->data = [
            'hello' => [
                'world'
            ],
        ];
        $this->logistic = new Logistic($this->info, $this->status, $this->data);
    }

    public function testGetSetInfo()
    {
        $this->assertSame($this->info, $this->logistic->getWaybill());
        $data = $this->createMock(Waybill::class);
        $this->logistic->setWaybill($data);
        $this->assertNotSame($this->info, $this->logistic->getWaybill());
        $this->assertSame($data, $this->logistic->getWaybill());
    }

    public function testGetSetStatus()
    {
        $this->assertSame($this->status, $this->logistic->getStatus());
        $status = $this->createMock(LogisticStatus::class);
        $this->logistic->setStatus($status);
        $this->assertSame($status, $this->logistic->getStatus());
    }

    public function testGetSetData(): void
    {
        $this->assertSame($this->data, $this->logistic->getData());
        $data = ['1', '2', 3];
        $this->logistic->setData($data);
        $this->assertSame($data, $this->logistic->getData());
        $this->logistic->setData(null);
        $this->assertNull($this->logistic->getData());
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

    public function testConstructWithTooBigData(): void
    {
        $this->expectException(LogisticDataTooBigException::class);
        $data = [];
        for ($i = 1; $i <= 1000; $i++) {
            $data[md5(random_bytes(10))] = md5(random_bytes(10));
        }
        new Logistic($this->info, $this->status, $data);
    }

    public function testTagDefaultsToNull(): void
    {
        $logistic = new Logistic($this->info, $this->status, $this->data);
        $this->assertNull($logistic->getTag());
    }

    public function testGetSetTag(): void
    {
        $this->assertNull($this->logistic->getTag());

        $tag1 = 'test-tag-1';
        $this->logistic->setTag($tag1);
        $this->assertSame($tag1, $this->logistic->getTag());

        $this->logistic->setTag(null);
        $this->assertNull($this->logistic->getTag());
    }

    public function testSetTooLongTag(): void
    {
        $this->expectException(LogisticTagTooLongException::class);
        $this->logistic->setTag(str_repeat('a', Logistic::MAX_TAG_LENGTH + 1));
    }

}
