<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticStatusTooLongException;
use XAKEPEHOK\EnumHelper\EnumHelper;
use XAKEPEHOK\EnumHelper\Exception\OutOfEnumException;

class LogisticTrackStatus extends EnumHelper
{

    const UNREGISTERED = -1;
    const CREATED = 0;
    const ACCEPTED = 100;
    const PACKED = 150;
    const IN_TRANSIT = 200;
    const ARRIVED = 300;
    const ON_DELIVERY = 400;
    const PENDING = 450;
    const DELIVERED = 500;
    const PAID = 550;
    const RETURNED = 600;
    const RETURNING_TO_SENDER = 650;
    const DELIVERED_TO_SENDER = 699;

    private int $code;
    private ?string $text;

    /**
     * LogisticStatus constructor.
     * @param int $code
     * @param string|null $text
     * @throws OutOfEnumException
     * @throws LogisticStatusTooLongException
     */
    public function __construct(int $code, string $text = null)
    {
        self::guardValidValue($code);

        if (mb_strlen($text) > 250) {
            throw new LogisticStatusTooLongException('Track status length should be less than 250 chars');
        }

        $this->code = $code;

        $text = trim($text);
        $this->text = empty($text) ? null : $text;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public static function values(): array
    {
        return [
            self::UNREGISTERED,
            self::CREATED,
            self::ACCEPTED,
            self::PACKED,
            self::IN_TRANSIT,
            self::ARRIVED,
            self::ON_DELIVERY,
            self::PENDING,
            self::DELIVERED,
            self::PAID,
            self::RETURNED,
            self::RETURNING_TO_SENDER,
            self::DELIVERED_TO_SENDER,
        ];
    }
}