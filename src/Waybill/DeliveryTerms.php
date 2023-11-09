<?php
/**
 * Created for plugin-component-logistic
 * Date: 19.01.2021
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic\Waybill;


use JsonSerializable;
use SalesRender\Plugin\Components\Logistic\Exceptions\DeliveryTermsException;

class DeliveryTerms implements JsonSerializable
{

    private int $minHours;
    private int $maxHours;

    public function __construct(int $minHours, int $maxHours)
    {
        if ($minHours < 0) {
            throw new DeliveryTermsException('Min delivery terms should not be negative', 1);
        }

        if ($maxHours > 8760) {
            throw new DeliveryTermsException('Max delivery terms should not be great than 8760', 2);
        }

        if ($minHours > $maxHours) {
            throw new DeliveryTermsException('Min delivery terms should not be less than max', 3);
        }

        $this->minHours = $minHours;
        $this->maxHours = $maxHours;
    }

    public function getMinHours(): int
    {
        return $this->minHours;
    }

    public function getMaxHours(): int
    {
        return $this->maxHours;
    }

    public function jsonSerialize(): array
    {
        return [
            'minHours' => $this->minHours,
            'maxHours' => $this->maxHours,
        ];
    }
}