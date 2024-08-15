<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic\Waybill;


use JsonSerializable;
use XAKEPEHOK\EnumHelper\EnumHelper;
use XAKEPEHOK\EnumHelper\Exception\OutOfEnumException;

class DeliveryType extends EnumHelper implements JsonSerializable
{

    const SELF_PICKUP = 'SELF_PICKUP';
    const PICKUP_POINT = 'PICKUP_POINT';
    const COURIER = 'COURIER';

    protected string $type;

    /**
     * LogisticDelivery constructor.
     * @param string $type
     * @throws OutOfEnumException
     */
    public function __construct(string $type)
    {
        self::guardValidValue($type);
        $this->type = $type;
    }

    public function get(): string
    {
        return $this->type;
    }

    public function getAsString(): string
    {
        return self::switchCase($this->type, [
            self::COURIER => 'COURIER',
            self::PICKUP_POINT => 'PICKUP_POINT',
            self::SELF_PICKUP => 'SELF_PICKUP',
        ]);
    }

    public static function values(): array
    {
        return [
            self::COURIER,
            self::PICKUP_POINT,
            self::SELF_PICKUP
        ];
    }

    public function jsonSerialize(): string
    {
        return $this->type;
    }
}