<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic\Waybill;


use JsonSerializable;
use SalesRender\Components\MoneyValue\MoneyValue;
use SalesRender\Plugin\Components\Logistic\Exceptions\NegativePriceException;
use XAKEPEHOK\ValueObjectBuilder\VOB;

class Waybill implements JsonSerializable
{

    protected ?Track $track = null;

    protected ?MoneyValue $price = null;

    protected ?DeliveryTerms $deliveryTerms = null;

    protected ?DeliveryType $deliveryType = null;

    protected ?bool $cod = null;

    /**
     * Waybill constructor.
     * @param Track|null $track
     * @param MoneyValue|null $price
     * @param DeliveryTerms|null $deliveryTerms
     * @param DeliveryType|null $deliveryType
     * @param bool|null $cod
     * @throws NegativePriceException
     */
    public function __construct(
        Track $track = null,
        MoneyValue $price = null,
        DeliveryTerms $deliveryTerms = null,
        DeliveryType $deliveryType = null,
        bool $cod = null
    )
    {
        $this->track = $track;

        $this->guardPrice($price);
        $this->price = $price;

        $this->deliveryTerms = $deliveryTerms;
        $this->deliveryType = $deliveryType;
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
     * @throws NegativePriceException
     */
    public function setPrice(?MoneyValue $price): Waybill
    {
        $this->guardPrice($price);

        $clone = clone $this;
        $clone->price = $price;

        return $clone;
    }

    public function getDeliveryTerms(): ?DeliveryTerms
    {
        return $this->deliveryTerms;
    }

    public function setDeliveryTerms(?DeliveryTerms $deliveryTerms): Waybill
    {
        $clone = clone $this;
        $clone->deliveryTerms = $deliveryTerms;
        return $clone;
    }

    public function getDeliveryType(): ?DeliveryType
    {
        return $this->deliveryType;
    }

    public function setDeliveryType(?DeliveryType $deliveryType): Waybill
    {
        $clone = clone $this;
        $clone->deliveryType = $deliveryType;

        return $clone;
    }

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
            'price' => $this->getPrice() ? $this->getPrice()->getAmount() : null,
            'deliveryTerms' => $this->getDeliveryTerms(),
            'deliveryType' => $this->getDeliveryType(),
            'cod' => $this->isCod(),
        ];
    }

    /**
     * @param MoneyValue|null $price
     * @throws NegativePriceException
     */
    private function guardPrice(?MoneyValue $price): void
    {
        if ($price && $price->getAmount() < 0) {
            throw new NegativePriceException('Logistic price can not be negative');
        }
    }

    public static function createFromArray(array $data): self
    {
        $terms = $data['deliveryTerms'] ?? [];
        return new Waybill(
            VOB::build(Track::class, $data['track'] ?? null),
            VOB::build(MoneyValue::class, $data['price'] ?? null),
            VOB::buildFromValues(DeliveryTerms::class, [$terms['minHours'] ?? null, $terms['maxHours'] ?? null]),
            VOB::build(DeliveryType::class, $data['deliveryType'] ?? null),
            $data['cod'] ?? null,
        );
    }
}