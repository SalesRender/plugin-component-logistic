<?php
/**
 * Created for plugin-component-logistic
 * Date: 14.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticDataTooBigException;

class Logistic
{

    private LogisticInfo $info;

    private LogisticStatus $status;

    protected ?array $data = null;

    /**
     * Logistic constructor.
     * @param LogisticInfo $info
     * @param LogisticStatus $status
     * @param array|null $data
     * @throws LogisticDataTooBigException
     */
    public function __construct(LogisticInfo $info, LogisticStatus $status, array $data = null)
    {
        $this->info = $info;
        $this->status = $status;
        $this->setData($data);
    }

    public function getInfo(): LogisticInfo
    {
        return $this->info;
    }

    public function setInfo(LogisticInfo $info): void
    {
        $this->info = $info;
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