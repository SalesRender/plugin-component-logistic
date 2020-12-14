<?php
/**
 * Created for plugin-component-logistic
 * Date: 14.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


class Logistic
{

    private LogisticData $data;

    /** @var LogisticStatus[] */
    private array $statuses = [];

    /**
     * Logistic constructor.
     * @param LogisticData $data
     * @param LogisticStatus[] $statuses
     */
    public function __construct(LogisticData $data, array $statuses = [])
    {
        $this->data = $data;
        foreach ($statuses as $status) {
            $this->addStatus($status);
        }
    }

    public function getData(): LogisticData
    {
        return $this->data;
    }

    public function setData(LogisticData $data): void
    {
        $this->data = $data;
    }

    public function getStatus(): LogisticStatus
    {
        return $this->statuses[array_key_last($this->statuses)];
    }

    /**
     * @return LogisticStatus[]
     */
    public function getStatuses(): array
    {
        return array_reverse($this->statuses);
    }

    public function addStatus(LogisticStatus $status): void
    {
        $this->statuses[] = $status;
    }

}