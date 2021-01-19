<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic\Waybill;


use JsonSerializable;
use Leadvertex\Components\MoneyValue\MoneyValue;
use Leadvertex\Plugin\Components\Logistic\Exceptions\NegativeLogisticPriceException;
use Leadvertex\Plugin\Components\Logistic\Exceptions\ShippingTimeException;

class Waybill implements JsonSerializable
{

    protected ?Track $track = null;

    protected ?MoneyValue $price = null;

    protected ?int $shippingTime = null;

    protected ?Delivery $delivery = null;

    protected ?bool $cod = null;

    public function __construct(Track $track = null, MoneyValue $price = null, int $shippingTimeInHours = null, Delivery $delivery = null, bool $cod = null)
    {
        $this->track = $track;

        $this->guardPrice($price);
        $this->price = $price;

        $this->guardShippingTime($shippingTimeInHours);
        $this->shippingTime = $shippingTimeInHours;

        $this->delivery = $delivery;
        $this->cod = $cod;
    }

    public function getTrack(): ?Track
    {
        return $this->track;
    }

    public function setTrack(?Track $track): self
    {
        $clone = clone $this;
        $clone->track = $track;

        return $clone;
    }

    public function getPrice(): ?MoneyValue
    {
        return $this->price;
    }

    /**
     * @param MoneyValue|null $price
     * @return Waybill
     * @throws NegativeLogisticPriceException
     */
    public function setPrice(?MoneyValue $price): Waybill
    {
        $this->guardPrice($price);

        $clone = clone $this;
        $clone->price = $price;

        return $clone;
    }

    public function getShippingTime(): ?int
    {
        return $this->shippingTime;
    }

    /**
     * @param int|null $hours
     * @return Waybill
     * @throws ShippingTimeException
     */
    public function setShippingTime(?int $hours): Waybill
    {
        $this->guardShippingTime($hours);

        $clone = clone $this;
        $clone->shippingTime = $hours;

        return $clone;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): Waybill
    {
        $clone = clone $this;
        $clone->delivery = $delivery;

        return $clone;
    }

    /**
     * @return bool|null
     */
    public function isCod(): ?bool
    {
        return $this->cod;
    }

    public function setCod(?bool $cod): Waybill
    {
        $clone = clone $this;
        $clone->cod = $cod;

        return $clone;
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

    private function guardPrice(?MoneyValue $price): void
    {
        if ($price && $price->getAmount() < 0) {
            throw new NegativeLogisticPriceException('Logistic price can not be negative');
        }
    }

    private function guardShippingTime(?int $hours): void
    {
        if ($hours < 0 || $hours > 5000) {
            throw new ShippingTimeException('Shipping time (in hours) should be between 0 and 5000');
        }
    }
}