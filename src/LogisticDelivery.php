<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


use JsonSerializable;
use XAKEPEHOK\EnumHelper\EnumHelper;
use XAKEPEHOK\EnumHelper\Exception\OutOfEnumException;

class LogisticDelivery extends EnumHelper implements JsonSerializable
{

    const SELF_PICKUP = 100;
    const PICKUP_POINT = 200;
    const COURIER = 300;

    private int $method;

    /**
     * LogisticDelivery constructor.
     * @param string $method
     * @throws OutOfEnumException
     */
    public function __construct(string $method)
    {
        self::guardValidValue($method);
        $this->method = $method;
    }

    public function get(): int
    {
        return $this->method;
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
        return $this->method;
    }
}