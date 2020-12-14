<?php
/**
 * Created for plugin-component-logistic
 * Date: 14.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;

use PHPUnit\Framework\TestCase;

class LogisticTest extends TestCase
{

    private LogisticData $data;
    private LogisticStatus $status;
    private Logistic $logistic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = $this->createMock(LogisticData::class);
        $this->status = $this->createMock(LogisticStatus::class);
        $this->logistic = new Logistic($this->data, $this->status);
    }

    public function testGetSetData()
    {
        $this->assertSame($this->data, $this->logistic->getData());
        $data = $this->createMock(LogisticData::class);
        $this->logistic->setData($data);
        $this->assertNotSame($this->data, $this->logistic->getData());
        $this->assertSame($data, $this->logistic->getData());
    }

    public function testGetSetStatus()
    {
        $this->assertSame($this->status, $this->logistic->getStatus());
        $status = $this->createMock(LogisticStatus::class);
        $this->logistic->setStatus($status);
        $this->assertSame($status, $this->logistic->getStatus());
    }

}
