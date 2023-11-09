<?php
/**
 * Created for plugin-component-logistic
 * Date: 14.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic;


use SalesRender\Plugin\Components\Logistic\Exceptions\LogisticDataTooBigException;
use SalesRender\Plugin\Components\Logistic\Waybill\Waybill;

class Logistic
{

    protected Waybill $waybill;

    protected LogisticStatus $status;

    protected ?array $data = null;

    /**
     * Logistic constructor.
     * @param Waybill $waybill
     * @param LogisticStatus $status
     * @param array|null $data
     * @throws LogisticDataTooBigException
     */
    public function __construct(Waybill $waybill, LogisticStatus $status, array $data = null)
    {
        $this->waybill = $waybill;
        $this->status = $status;
        $this->setData($data);
    }

    public function getWaybill(): Waybill
    {
        return $this->waybill;
    }

    public function setWaybill(Waybill $waybill): void
    {
        $this->waybill = $waybill;
    }

    public function getStatus(): LogisticStatus
    {
        return $this->status;
    }

    public function setStatus(LogisticStatus $status): void
    {
        $this->status = $status;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     * @throws LogisticDataTooBigException
     */
    public function setData(?array $data): void
    {
        if (is_null($data)) {
            $this->data = null;
            return;
        }

        $size = mb_strlen(serialize($data), '8bit');
        if ($size > 2 * 1024) {
            throw new LogisticDataTooBigException("Logistic data size is {$size} bytes, but max is 2048");
        }

        $this->data = $data;
    }

}