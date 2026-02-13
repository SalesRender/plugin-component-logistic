# salesrender/plugin-component-logistic

Domain model component for logistics plugins in the SalesRender ecosystem. Provides value objects and data structures for shipments, waybills, delivery tracking, status management, pickup offices, and delivery terms.

## Installation

```bash
composer require salesrender/plugin-component-logistic
```

## Requirements

| Requirement | Version |
|---|---|
| PHP | >= 7.4 |
| ext-json | * |
| ext-mbstring | * |
| xakepehok/enum-helper | ^0.1.0 |
| salesrender/component-address | ^1.0.0 |
| spatie/opening-hours | ^2.10 |
| xakepehok/value-object-builder | ^0.1.2 |
| php-dto/uri | ^0.1.0 |

## Overview

This package defines the core domain types used by all SalesRender logistics plugins. It is used both when creating waybills (initial shipment registration) and when tracking delivery statuses over time. The component is consumed by [`plugin-core-logistic`](https://github.com/SalesRender/plugin-core-logistic) and by individual carrier plugins (CDEK, Nova Poshta, Belpost, InPost, and many others).

## Key Classes

### `Logistic`

**Namespace:** `SalesRender\Plugin\Components\Logistic`

Top-level aggregate that combines a waybill with its current status and optional arbitrary data.

| Method | Return Type | Description |
|---|---|---|
| `__construct(Waybill $waybill, LogisticStatus $status, ?array $data = null)` | | Creates a logistic record. Throws `LogisticDataTooBigException` if `$data` exceeds 2048 bytes. |
| `getWaybill()` | `Waybill` | Returns the waybill. |
| `setWaybill(Waybill $waybill)` | `void` | Replaces the waybill. |
| `getStatus()` | `LogisticStatus` | Returns the current status. |
| `setStatus(LogisticStatus $status)` | `void` | Replaces the current status. |
| `getData()` | `?array` | Returns optional arbitrary data (max 2 KB serialized). |
| `setData(?array $data)` | `void` | Sets arbitrary data. Throws `LogisticDataTooBigException` if over 2048 bytes. |

### `LogisticStatus`

**Namespace:** `SalesRender\Plugin\Components\Logistic`

Represents a shipment status at a point in time. Extends `EnumHelper` and implements `JsonSerializable`.

| Method | Return Type | Description |
|---|---|---|
| `__construct(int $code, string $text = '', ?int $timestamp = null, ?LogisticOffice $office = null)` | | Creates a status. Validates code against enum. Throws `LogisticStatusTooLongException` if text > 250 chars. Timestamp defaults to `time()`. |
| `getTimestamp()` | `int` | Unix timestamp of this status event. |
| `getCode()` | `int` | Numeric status code (see table below). |
| `getText()` | `?string` | Human-readable status description. |
| `getHash()` | `string` | MD5 hash of the serialized status, used for deduplication. |
| `getOffice()` | `?LogisticOffice` | Office/pickup point associated with this status. |
| `values()` | `array` | Returns all valid status codes. |
| `code2strings()` | `array` | Returns associative array mapping codes to string names. |
| `jsonSerialize()` | `array` | Returns `['timestamp', 'code', 'text', 'office']`. |

#### Status Code Reference

| Constant | Code | Description |
|---|---|---|
| `UNREGISTERED` | `-1` | Shipment is not registered or has been deleted in the carrier system. |
| `CREATED` | `1` | Waybill created in the plugin but not yet sent to the carrier. |
| `REGISTERED` | `50` | Registered with the carrier (tracking number assigned). |
| `ACCEPTED` | `100` | Accepted at the carrier warehouse / customs processing. |
| `PACKED` | `150` | Shipment packed and ready for dispatch. |
| `IN_TRANSIT` | `200` | In transit between cities or warehouses. |
| `ARRIVED` | `300` | Arrived at the destination city warehouse or pickup point. |
| `ON_DELIVERY` | `400` | Out for delivery by courier. |
| `PENDING` | `450` | Delivery attempt failed (recipient absent, rescheduled, etc.). |
| `DELIVERED` | `500` | Successfully delivered to the recipient. |
| `PAID` | `550` | Delivered and cash-on-delivery payment collected. |
| `RETURNED` | `600` | Shipment returned (recipient refused, undeliverable). |
| `RETURNING_TO_SENDER` | `650` | Shipment is in transit back to the sender. |
| `DELIVERED_TO_SENDER` | `699` | Return shipment delivered back to the sender. |
| `UNKNOWN` | `1000` | Status could not be mapped to any known code. |

### `Waybill`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Waybill`

Immutable value object representing a shipping waybill. Setters return a cloned instance (immutable pattern).

| Method | Return Type | Description |
|---|---|---|
| `__construct(?Track $track = null, ?float $shippingCost = null, ?DeliveryTerms $deliveryTerms = null, ?DeliveryType $deliveryType = null, ?bool $cod = null)` | | Creates a waybill. Throws `NegativePriceException` if shipping cost is negative. |
| `getTrack()` | `?Track` | Returns the tracking number. |
| `setTrack(?Track $track)` | `Waybill` | Returns a new `Waybill` with the updated track. |
| `getShippingCost()` | `?float` | Shipping cost (in the currency's base unit). |
| `setShippingCost(?float $shippingCost)` | `Waybill` | Returns a new `Waybill` with the updated cost. Throws `NegativePriceException` if negative. |
| `getDeliveryTerms()` | `?DeliveryTerms` | Estimated delivery time range. |
| `setDeliveryTerms(?DeliveryTerms $deliveryTerms)` | `Waybill` | Returns a new `Waybill` with updated terms. |
| `getDeliveryType()` | `?DeliveryType` | Type of delivery. |
| `setDeliveryType(?DeliveryType $deliveryType)` | `Waybill` | Returns a new `Waybill` with updated type. |
| `isCod()` | `?bool` | Whether cash-on-delivery is enabled. |
| `setCod(?bool $cod)` | `Waybill` | Returns a new `Waybill` with updated COD flag. |
| `jsonSerialize()` | `array` | Returns the waybill as an associative array. |
| `createFromArray(array $data)` | `Waybill` | Static factory that reconstructs a `Waybill` from a serialized array. |

### `Track`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Waybill`

Value object for a tracking number.

| Method | Return Type | Description |
|---|---|---|
| `__construct(string $track)` | | Validates: 6-36 chars, only `A-Z`, `0-9`, `-`, `_`. Throws `LogisticTrackException` on invalid input. |
| `get()` | `string` | Returns the tracking number. |
| `__toString()` | `string` | Returns the tracking number as a string. |
| `jsonSerialize()` | `string` | Returns the tracking number. |

### `DeliveryTerms`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Waybill`

Value object for estimated delivery time range in hours.

| Method | Return Type | Description |
|---|---|---|
| `__construct(int $minHours, int $maxHours)` | | Validates: `$minHours >= 0`, `$maxHours <= 8760` (1 year), `$minHours <= $maxHours`. Throws `DeliveryTermsException`. |
| `getMinHours()` | `int` | Minimum delivery time in hours. |
| `getMaxHours()` | `int` | Maximum delivery time in hours. |
| `jsonSerialize()` | `array` | Returns `['minHours' => ..., 'maxHours' => ...]`. |

### `DeliveryType`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Waybill`

Enum value object for the delivery method. Extends `EnumHelper`.

| Constant | Value | Description |
|---|---|---|
| `SELF_PICKUP` | `'SELF_PICKUP'` | Sender delivers to carrier themselves. |
| `PICKUP_POINT` | `'PICKUP_POINT'` | Recipient picks up at a designated point. |
| `COURIER` | `'COURIER'` | Courier delivery to the recipient's address. |

| Method | Return Type | Description |
|---|---|---|
| `__construct(string $type)` | | Validates against allowed values. Throws `OutOfEnumException`. |
| `get()` | `string` | Returns the delivery type string. |
| `getAsString()` | `string` | Returns the type as a readable string. |
| `values()` | `array` | Returns `['SELF_PICKUP', 'PICKUP_POINT', 'COURIER']`. |
| `jsonSerialize()` | `string` | Returns the delivery type string. |

### `LogisticOffice`

**Namespace:** `SalesRender\Plugin\Components\Logistic`

Represents a carrier office or pickup point with address, phone numbers, and opening hours.

| Method | Return Type | Description |
|---|---|---|
| `__construct(?Address $address, array $phones, ?OpeningHours $openingHours)` | | Validates phone numbers against pattern `^\+?\d{9,16}$`. Throws `LogisticOfficePhoneException`. |
| `getAddress()` | `?Address` | Returns the office address. |
| `getPhones()` | `array` | Returns the list of phone numbers. |
| `getOpeningHours()` | `?OpeningHours` | Returns opening hours schedule. |
| `jsonSerialize()` | `array` | Returns `['address', 'phones', 'openingHours']`. |
| `createFromArray(?array $data)` | `?self` | Static factory that reconstructs from an array. Returns `null` if input is `null`. |

### `OpeningHours`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Components`

Wrapper around `spatie/opening-hours` for office schedule validation.

| Method | Return Type | Description |
|---|---|---|
| `__construct(array $schedule)` | | Validates schedule via `Spatie\OpeningHours`. Throws `OpeningHoursException`. |
| `getSchedule()` | `array` | Returns the validated schedule array. |
| `jsonSerialize()` | `array` | Returns the schedule. |

### `ShippingAttachment`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Components`

Represents a file attachment (label, invoice, etc.) associated with a shipment.

| Method | Return Type | Description |
|---|---|---|
| `__construct(string $name, Uri $uri)` | | Validates name: 1-255 characters. Throws `ShippingAttachmentException`. |
| `getName()` | `string` | Returns the attachment name. |
| `getUri()` | `Uri` | Returns the attachment URI. |
| `createFromArray(array $data)` | `self` | Static factory from `['name' => ..., 'uri' => ...]`. |
| `jsonSerialize()` | `array` | Returns `['name' => ..., 'uri' => ...]`. |

## Exceptions

| Exception | Thrown By | Condition |
|---|---|---|
| `DeliveryTermsException` | `DeliveryTerms` | Invalid min/max hours (negative, exceeds 8760, or min > max). |
| `LogisticDataTooBigException` | `Logistic` | Data array exceeds 2048 bytes when serialized. |
| `LogisticOfficePhoneException` | `LogisticOffice` | Phone number does not match `^\+?\d{9,16}$`. |
| `LogisticStatusTooLongException` | `LogisticStatus` | Status text exceeds 250 characters. |
| `LogisticTrackException` | `Track` | Track number invalid (not 6-36 chars or contains disallowed characters). |
| `NegativePriceException` | `Waybill` | Shipping cost is negative. |
| `OpeningHoursException` | `OpeningHours` | Invalid schedule format (Spatie validation). |
| `ShippingAttachmentException` | `ShippingAttachment` | Attachment name is empty or exceeds 255 characters. |

## Usage Examples

### Creating a waybill with a logistic record

Taken from `plugin-logistic-example` (`WaybillHandler`):

```php
use SalesRender\Plugin\Components\Logistic\Logistic;
use SalesRender\Plugin\Components\Logistic\LogisticStatus;
use SalesRender\Plugin\Components\Logistic\Waybill\DeliveryTerms;
use SalesRender\Plugin\Components\Logistic\Waybill\DeliveryType;
use SalesRender\Plugin\Components\Logistic\Waybill\Track;
use SalesRender\Plugin\Components\Logistic\Waybill\Waybill;

// Create a tracking number
$track = new Track($data->get('waybill.track'));

// Build delivery terms (hours)
$terms = new DeliveryTerms(24, 72); // 1-3 days

// Create the waybill
$waybill = new Waybill(
    $track,
    $price,                                       // shipping cost
    $terms,                                       // delivery terms
    new DeliveryType(DeliveryType::PICKUP_POINT), // delivery type
    true                                          // cash on delivery
);

// Build the logistic aggregate
$logistic = new Logistic(
    $waybill,
    new LogisticStatus(LogisticStatus::CREATED, 'Waybill created')
);
```

### Mapping carrier statuses to LogisticStatus codes

Taken from `plugin-logistic-cdek` (`TrackingHelper`):

```php
use SalesRender\Plugin\Components\Logistic\LogisticStatus;

// Map CDEK API statuses to LogisticStatus codes
const STATUS_CODES = [
    'CREATED'                              => LogisticStatus::REGISTERED,
    'ACCEPTED'                             => LogisticStatus::ACCEPTED,
    'RECEIVED_AT_SHIPMENT_WAREHOUSE'       => LogisticStatus::ACCEPTED,
    'SENT_TO_RECIPIENT_CITY'               => LogisticStatus::IN_TRANSIT,
    'ACCEPTED_AT_RECIPIENT_CITY_WAREHOUSE' => LogisticStatus::ARRIVED,
    'TAKEN_BY_COURIER'                     => LogisticStatus::ON_DELIVERY,
    'DELIVERED'                            => LogisticStatus::DELIVERED,
    'NOT_DELIVERED'                        => LogisticStatus::RETURNED,
];

// Create a status from tracking data
$status = new LogisticStatus(
    self::STATUS_CODES[$apiStatusCode],
    mb_substr($statusText, 0, 255),
    (new DateTime($statusDate))->getTimestamp(),
    $logisticOffice
);
```

### Creating a LogisticOffice with opening hours

Taken from `plugin-logistic-cdek` (`TrackingHelper`):

```php
use SalesRender\Components\Address\Address;
use SalesRender\Plugin\Components\Logistic\Components\OpeningHours;
use SalesRender\Plugin\Components\Logistic\LogisticOffice;

$openingHours = new OpeningHours([
    'monday'    => ['09:00-18:00'],
    'tuesday'   => ['09:00-18:00'],
    'wednesday' => ['09:00-18:00'],
    'thursday'  => ['09:00-18:00'],
    'friday'    => ['09:00-18:00'],
    'saturday'  => ['10:00-15:00'],
    'sunday'    => [],
]);

$office = new LogisticOffice(
    new Address('', 'Moscow', '123 Main St'),
    ['+79001234567'],
    $openingHours
);
```

### Simple waybill creation (Nova Poshta)

Taken from `plugin-logistic-novaposhta` (`WaybillHandler`):

```php
use SalesRender\Plugin\Components\Logistic\Logistic;
use SalesRender\Plugin\Components\Logistic\LogisticStatus;
use SalesRender\Plugin\Components\Logistic\Waybill\Track;
use SalesRender\Plugin\Components\Logistic\Waybill\Waybill;

$track = null;
if (!empty($trackNumber)) {
    $track = new Track($trackNumber);
}

$waybill = new Waybill($track);

$logistic = new Logistic(
    $waybill,
    new LogisticStatus(LogisticStatus::CREATED, 'Waybill created'),
    $deliveryData // optional metadata (max 2 KB)
);
```

### Tracking and status updates (Nova Poshta)

Taken from `plugin-logistic-novaposhta` (`TrackingCommand`):

```php
use SalesRender\Plugin\Components\Logistic\LogisticStatus;

// Status mapping table
const TRACKING_STATUSES_TABLE = [
    1   => ['code' => LogisticStatus::REGISTERED, 'text' => 'Sender created the waybill'],
    4   => ['code' => LogisticStatus::IN_TRANSIT, 'text' => 'Shipment en route to city'],
    7   => ['code' => LogisticStatus::ARRIVED,    'text' => 'Arrived at branch'],
    9   => ['code' => LogisticStatus::DELIVERED,  'text' => 'Shipment received'],
    11  => ['code' => LogisticStatus::PAID,       'text' => 'Delivered, COD payment issued'],
    103 => ['code' => LogisticStatus::RETURNED,   'text' => 'Recipient refused the shipment'],
];

// Create a status and add it to the track
$status = new LogisticStatus(
    $statusMapping['code'],
    $statusDescription,
    $statusTimestamp
);

$track->addStatus($status);
$track->save();
```

### Using batch shipping with random statuses (example plugin)

Taken from `plugin-logistic-example` (`BatchShippingHandler`):

```php
use SalesRender\Plugin\Components\Logistic\LogisticStatus;
use SalesRender\Plugin\Components\Logistic\Waybill\DeliveryType;

$data[$orderId] = [
    'waybill' => [
        'price' => rand(100, 350) * 100,
        'track' => 'TN' . rand(10000000, 99999999),
        'deliveryTerms' => [
            'minHours' => rand(1, 6),
            'maxHours' => rand(6, 72),
        ],
        'deliveryType' => DeliveryType::values()[rand(0, count(DeliveryType::values()) - 1)],
        'cod' => (bool) rand(0, 1),
    ],
    'status' => [
        'code' => LogisticStatus::CREATED,
        'text' => 'Created status',
        'timestamp' => time(),
        'office' => null,
    ],
];
```

## Dependencies

| Package | Purpose |
|---|---|
| `salesrender/component-address` | `Address` and `Location` value objects |
| `xakepehok/enum-helper` | Base class for enum types (`LogisticStatus`, `DeliveryType`) |
| `xakepehok/value-object-builder` | Factory for building value objects from arrays |
| `spatie/opening-hours` | Opening hours validation |
| `php-dto/uri` | URI value object for attachments |

## See Also

- [salesrender/plugin-core-logistic](https://github.com/SalesRender/plugin-core-logistic) -- core logistic plugin infrastructure (Track model, WaybillHandler, BatchShippingHandler)
- [salesrender/component-address](https://github.com/SalesRender/component-address) -- Address and Location value objects
- [salesrender/plugin-component-special-request](https://github.com/SalesRender/plugin-component-special-request) -- used by Track to send status notification requests
- [salesrender/plugin-component-batch](https://github.com/SalesRender/plugin-component-batch) -- batch processing infrastructure used by shipping handlers
