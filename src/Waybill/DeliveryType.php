<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic\Waybill;


use JsonSerializable;
use XAKEPEHOK\EnumHelper\EnumHelper;
use XAKEPEHOK\EnumHelper\Exception\OutOfEnumException;

class DeliveryType extends EnumHelper implements JsonSerializable
{

    const SELF_PICKUP = 100;
    const PICKUP_POINT = 200;
    const COURIER = 300;

    protected int $type;

    /**
     * LogisticDelivery constructor.
     * @param int $type
     * @throws OutOfEnumException
     */
    public function __construct(int $type)
    {
        self::guardValidValue($type);
        $this->type = $type;
    }

    public function get(): int
    {
        return $this->type;
    }

    public static function values(): array
    {
        return [
            self::COURIER,
            self::PICKUP_POINT,
            self::SELF_PICKUP
        ];
    }

    public function jsonSerialize(): int
    {
        return $this->type;
    }
}