<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


use JsonSerializable;
use Leadvertex\Components\MoneyValue\MoneyValue;
use Leadvertex\Plugin\Components\Logistic\Exceptions\NegativeLogisticPriceException;
use Leadvertex\Plugin\Components\Logistic\Exceptions\ShippingTimeException;

class LogisticInfo implements JsonSerializable
{

    protected ?LogisticTrack $track = null;

    protected ?MoneyValue $price = null;

    protected ?int $shippingTime = null;

    protected ?LogisticDelivery $delivery = null;

    protected ?bool $cod = null;

    public function getTrack(): ?LogisticTrack
    {
        return $this->track;
    }

    public function setTrack(?LogisticTrack $track): self
    {
        $this->track = $track;
        return $this;
    }

    public function getPrice(): ?MoneyValue
    {
        return $this->price;
    }

    /**
     * @param MoneyValue|null $price
     * @return LogisticInfo
     * @throws NegativeLogisticPriceException
     */
    public function setPrice(?MoneyValue $price): LogisticInfo
    {
        if ($price && $price->getAmount() < 0) {
            throw new NegativeLogisticPriceException('Logistic price can not be negative');
        }

        $this->price = $price;
        return $this;
    }

    public function getShippingTime(): ?int
    {
        return $this->shippingTime;
    }

    /**
     * @param int|null $shippingTime
     * @return LogisticInfo
     * @throws ShippingTimeException
     */
    public function setShippingTime(?int $shippingTime): LogisticInfo
    {
        if ($shippingTime < 0 || $shippingTime > 5000) {
            throw new ShippingTimeException('Shipping time (in hours) should be between 0 and 5000');
        }

        $this->shippingTime = $shippingTime;
        return $this;
    }

    public function getDelivery(): ?LogisticDelivery
    {
        return $this->delivery;
    }

    public function setDelivery(?LogisticDelivery $delivery): LogisticInfo
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

    public function setCod(?bool $cod): LogisticInfo
    {
        $this->cod = $cod;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'track' => $this->getTrack(),
            'price' => $this->getPrice(),
            'shippingTime' => $this->getShippingTime(),
            'delivery' => $this->getDelivery(),
            'cod' => $this->isCod(),
        ];
    }
}