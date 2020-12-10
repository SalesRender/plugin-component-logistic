<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


use DateTimeImmutable;
use Leadvertex\Components\MoneyValue\MoneyValue;
use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticDataTooBigException;
use Leadvertex\Plugin\Components\Logistic\Exceptions\NegativeLogisticPriceException;
use Leadvertex\Plugin\Components\Logistic\Exceptions\ShippingTimeException;

class Logistic
{

    protected ?array $data = null;

    protected ?LogisticTrack $track = null;

    protected ?LogisticTrackStatus $status = null;

    protected ?DateTimeImmutable $statusChangedAt = null;

    protected ?MoneyValue $price = null;

    protected ?int $shippingTime = null;

    protected ?LogisticDelivery $delivery = null;

    protected ?bool $cod = null;

    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return Logistic
     * @throws LogisticDataTooBigException
     */
    public function setData(array $data): Logistic
    {
        $size = mb_strlen(serialize($data), '8bit');
        if ($size > 2 * 1024) {
            throw new LogisticDataTooBigException("Logistic data size is {$size} bytes, but max is 2048");
        }

        $this->data = $data;
        return $this;
    }

    public function getTrack(): ?LogisticTrack
    {
        return $this->track;
    }

    public function setTrack(?LogisticTrack $track): self
    {
        $this->track = $track;
        return $this;
    }

    public function getStatus(): ?LogisticTrackStatus
    {
        return $this->status;
    }

    public function getStatusChangedAt(): ?DateTimeImmutable
    {
        return $this->statusChangedAt;
    }

    /**
     * @param LogisticTrackStatus |null $status
     * @param DateTimeImmutable|null $dateTime
     * @return Logistic
     */
    public function setStatus(?LogisticTrackStatus $status, DateTimeImmutable $dateTime = null): Logistic
    {
        $this->status = $status;

        $dateTime = $dateTime ?? new DateTimeImmutable();
        $this->statusChangedAt = $dateTime;

        if (is_null($status)) {
            $this->statusChangedAt = null;
        }

        return $this;
    }

    /**
     * @return MoneyValue|null
     */
    public function getPrice(): ?MoneyValue
    {
        return $this->price;
    }

    /**
     * @param MoneyValue|null $price
     * @return Logistic
     * @throws NegativeLogisticPriceException
     */
    public function setPrice(?MoneyValue $price): Logistic
    {
        if ($price && $price->getAmount() < 0) {
            throw new NegativeLogisticPriceException('Logistic price can not be negative');
        }

        $this->price = $price;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getShippingTime(): ?int
    {
        return $this->shippingTime;
    }

    /**
     * @param int|null $shippingTime
     * @return Logistic
     * @throws ShippingTimeException
     */
    public function setShippingTime(?int $shippingTime): Logistic
    {
        if ($shippingTime < 0 || $shippingTime > 5000) {
            throw new ShippingTimeException('Shipping time (in hours) should be between 0 and 5000');
        }

        $this->shippingTime = $shippingTime;
        return $this;
    }

    /**
     * @return LogisticDelivery|null
     */
    public function getDelivery(): ?LogisticDelivery
    {
        return $this->delivery;
    }

    /**
     * @param LogisticDelivery|null $delivery
     * @return Logistic
     */
    public function setDelivery(?LogisticDelivery $delivery): Logistic
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isCod(): ?bool
    {
        return $this->cod;
    }

    /**
     * @param bool|null $cod
     * @return Logistic
     */
    public function setCod(?bool $cod): Logistic
    {
        $this->cod = $cod;
        return $this;
    }

}