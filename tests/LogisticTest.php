<?php
/**
 * Created for plugin-component-logistic
 * Date: 14.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;

use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticDataTooBigException;
use PHPUnit\Framework\TestCase;

class LogisticTest extends TestCase
{

    private LogisticInfo $info;
    private LogisticStatus $status;
    private array $data;
    private Logistic $logistic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->info = $this->createMock(LogisticInfo::class);
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
        $this->assertSame($this->info, $this->logistic->getInfo());
        $data = $this->createMock(LogisticInfo::class);
        $this->logistic->setInfo($data);
        $this->assertNotSame($this->info, $this->logistic->getInfo());
        $this->assertSame($data, $this->logistic->getInfo());
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

}
