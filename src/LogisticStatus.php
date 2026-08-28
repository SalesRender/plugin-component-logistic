<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic;


use InvalidArgumentException;
use JsonSerializable;
use SalesRender\Plugin\Components\Logistic\Exceptions\LogisticStatusTooLongException;
use XAKEPEHOK\EnumHelper\EnumHelper;
use XAKEPEHOK\EnumHelper\Exception\OutOfEnumException;

class LogisticStatus extends EnumHelper implements JsonSerializable
{

    const int UNREGISTERED = -1;
    const int CREATED = 1;
    const int REGISTERED = 50;
    const int ACCEPTED = 100;
    const int PACKED = 150;
    const int IN_TRANSIT = 200;
    const int ARRIVED = 300;
    const int ON_DELIVERY = 400;
    const int PENDING = 450;
    const int DELIVERED = 500;
    const int PAID = 550;
    const int RETURNED = 600;
    const int RETURNING_TO_SENDER = 650;
    const int DELIVERED_TO_SENDER = 699;
    const int UNKNOWN = 1000;

    protected int $timestamp;
    protected int $code;
    protected ?string $text;
    protected string $hash;
    protected ?LogisticOffice $office;
    protected ?int $notificationRevision = null;

    /**
     * LogisticStatus constructor.
     * @throws OutOfEnumException
     * @throws LogisticStatusTooLongException
     */
    public function __construct(int $code, string $text = '', ?int $timestamp = null, ?LogisticOffice $office = null)
    {
        self::guardValidValue($code);

        if (mb_strlen($text) > 250) {
            throw new LogisticStatusTooLongException('Track status length should be less than 250 chars');
        }

        $this->code = $code;

        $this->text = trim($text);

        $this->timestamp = $timestamp ?? time();

        $this->office = $office;

        $this->hash = $this->calculateHash();
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

    public function getOffice(): ?LogisticOffice
    {
        return $this->office;
    }

    public function getNotificationRevision(): ?int
    {
        return $this->notificationRevision;
    }

    /**
     * Sequence number of the notification for a specific shipping. Protects the status
     * from being overwritten by an older notification delivered out of order.
     * Does not participate in hash calculation.
     */
    public function withNotificationRevision(int $notificationRevision): self
    {
        if ($notificationRevision <= 0) {
            throw new InvalidArgumentException('Notification revision should be positive integer');
        }

        $clone = clone $this;
        $clone->notificationRevision = $notificationRevision;

        return $clone;
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
            self::UNKNOWN,
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
            self::UNKNOWN => 'UNKNOWN',
        ]);
    }

    public function jsonSerialize(): array
    {
        return [
            'timestamp' => $this->getTimestamp(),
            'code' => $this->getCode(),
            'text' => $this->getText(),
            'office' => $this->getOffice(),
            'notificationRevision' => $this->getNotificationRevision(),
        ];
    }

    /**
     * The hash is calculated from a fixed set of fields and does not depend on
     * notificationRevision: otherwise previously stored hashes in
     * Track::notificationsHashes would stop matching, and notifications
     * would be created again
     */
    private function calculateHash(): string
    {
        return md5(json_encode([
            'timestamp' => $this->getTimestamp(),
            'code' => $this->getCode(),
            'text' => $this->getText(),
            'office' => $this->getOffice(),
        ]));
    }
}