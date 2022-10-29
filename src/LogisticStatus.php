<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


use JsonSerializable;
use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticStatusTooLongException;
use XAKEPEHOK\EnumHelper\EnumHelper;
use XAKEPEHOK\EnumHelper\Exception\OutOfEnumException;

class LogisticStatus extends EnumHelper implements JsonSerializable
{

    const UNREGISTERED = -1;
    const CREATED = 1;
    const REGISTERED = 50;
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

    protected int $timestamp;
    protected int $code;
    protected ?string $text;
    protected string $hash;

    /**
     * LogisticStatus constructor.
     * @param int $code
     * @param string|null $text
     * @param int|null $timestamp
     * @throws LogisticStatusTooLongException
     * @throws OutOfEnumException
     */
    public function __construct(int $code, string $text = '', ?int $timestamp = null)
    {
        self::guardValidValue($code);

        if (mb_strlen($text) > 250) {
            throw new LogisticStatusTooLongException('Track status length should be less than 250 chars');
        }

        $this->code = $code;

        $text = trim($text);
        $this->text = empty($text) ? null : $text;

        $this->timestamp = $timestamp ?? time();

        $this->hash = md5(json_encode($this->jsonSerialize()));
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public static function values(): array
    {
        return [
            self::UNREGISTERED,
            self::CREATED,
            self::REGISTERED,
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

    public static function code2strings(): array
    {
        return self::associative([
            self::UNREGISTERED => 'UNREGISTERED',
            self::CREATED => 'CREATED',
            self::REGISTERED => 'REGISTERED',
            self::ACCEPTED => 'ACCEPTED',
            self::PACKED => 'PACKED',
            self::IN_TRANSIT => 'IN_TRANSIT',
            self::ARRIVED => 'ARRIVED',
            self::ON_DELIVERY => 'ON_DELIVERY',
            self::PENDING => 'PENDING',
            self::DELIVERED => 'DELIVERED',
            self::PAID => 'PAID',
            self::RETURNED => 'RETURNED',
            self::RETURNING_TO_SENDER => 'RETURNING_TO_SENDER',
            self::DELIVERED_TO_SENDER => 'DELIVERED_TO_SENDER',
        ]);
    }

    public function jsonSerialize(): array
    {
        return [
            'timestamp' => $this->getTimestamp(),
            'code' => $this->getCode(),
            'text' => $this->getText(),
        ];
    }
}