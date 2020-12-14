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

    private LogisticStatus $status_old;
    private LogisticStatus $status_new;

    private array $statuses;

    private Logistic $logistic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = $this->createMock(LogisticData::class);

        $this->status_old = $this->createMock(LogisticStatus::class);
        $this->statuses = [
            $this->createMock(LogisticStatus::class),
            $this->createMock(LogisticStatus::class),
            $this->status_old,
        ];

        $this->logistic = new Logistic($this->data, $this->statuses);

        $this->status_new = $this->createMock(LogisticStatus::class);
    }

    public function testGetSetData()
    {
        $this->assertSame($this->data, $this->logistic->getData());
        $data = $this->createMock(LogisticData::class);
        $this->logistic->setData($data);
        $this->assertNotSame($this->data, $this->logistic->getData());
        $this->assertSame($data, $this->logistic->getData());
    }

    public function testGetStatus()
    {
        $this->assertSame($this->status_old, $this->logistic->getStatus());
    }

    public function testGetStatuses()
    {
        $this->assertSame(
            array_reverse($this->statuses),
            $this->logistic->getStatuses()
        );
    }

    public function testAddStatus()
    {
        $this->logistic->addStatus($this->status_new);
        $this->assertSame($this->status_new, $this->logistic->getStatus());
        $this->assertCount(4, $this->logistic->getStatuses());
    }

}
