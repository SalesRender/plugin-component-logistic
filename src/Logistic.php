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

    private LogisticStatus $status;

    /**
     * Logistic constructor.
     * @param LogisticData $data
     * @param LogisticStatus $status
     */
    public function __construct(LogisticData $data, LogisticStatus $status)
    {
        $this->data = $data;
        $this->status = $status;
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
        return $this->status;
    }

    public function setStatus(LogisticStatus $status): void
    {
        $this->status = $status;
    }

}