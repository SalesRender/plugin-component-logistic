# salesrender/plugin-component-logistic

Компонент доменной модели для логистических плагинов экосистемы SalesRender. Предоставляет value-объекты и структуры данных для отправлений, накладных, отслеживания доставки, управления статусами, пунктов выдачи и сроков доставки.

## Установка

```bash
composer require salesrender/plugin-component-logistic
```

## Требования

| Требование | Версия |
|---|---|
| PHP | >= 7.4 |
| ext-json | * |
| ext-mbstring | * |
| xakepehok/enum-helper | ^0.1.0 |
| salesrender/component-address | ^1.0.0 |
| spatie/opening-hours | ^2.10 |
| xakepehok/value-object-builder | ^0.1.2 |
| php-dto/uri | ^0.1.0 |

## Обзор

Пакет определяет основные доменные типы, используемые всеми логистическими плагинами SalesRender. Он применяется как при создании накладных (первичная регистрация отправления), так и при отслеживании статусов доставки. Компонент используется [`plugin-core-logistic`](https://github.com/SalesRender/plugin-core-logistic) и отдельными плагинами перевозчиков (СДЭК, Новая Почта, Белпочта, InPost и многими другими).

## Основные классы

### `Logistic`

**Namespace:** `SalesRender\Plugin\Components\Logistic`

Агрегат верхнего уровня, объединяющий накладную с текущим статусом и произвольными дополнительными данными.

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(Waybill $waybill, LogisticStatus $status, ?array $data = null)` | | Создает запись. Бросает `LogisticDataTooBigException`, если `$data` превышает 2048 байт. |
| `getWaybill()` | `Waybill` | Возвращает накладную. |
| `setWaybill(Waybill $waybill)` | `void` | Заменяет накладную. |
| `getStatus()` | `LogisticStatus` | Возвращает текущий status. |
| `setStatus(LogisticStatus $status)` | `void` | Заменяет текущий status. |
| `getData()` | `?array` | Возвращает произвольные данные (максимум 2 КБ в сериализованном виде). |
| `setData(?array $data)` | `void` | Устанавливает произвольные данные. Бросает `LogisticDataTooBigException` при превышении 2048 байт. |

### `LogisticStatus`

**Namespace:** `SalesRender\Plugin\Components\Logistic`

Представляет status отправления в определенный момент времени. Наследует `EnumHelper` и реализует `JsonSerializable`.

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(int $code, string $text = '', ?int $timestamp = null, ?LogisticOffice $office = null)` | | Создает status. Валидирует код по enum. Бросает `LogisticStatusTooLongException`, если текст длиннее 250 символов. Timestamp по умолчанию равен `time()`. |
| `getTimestamp()` | `int` | Unix-метка времени события. |
| `getCode()` | `int` | Числовой код status (см. таблицу ниже). |
| `getText()` | `?string` | Текстовое описание status. |
| `getHash()` | `string` | MD5-хеш сериализованного status, используется для дедупликации. |
| `getOffice()` | `?LogisticOffice` | Офис/пункт выдачи, связанный с данным status. |
| `values()` | `array` | Возвращает все допустимые коды status. |
| `code2strings()` | `array` | Возвращает ассоциативный массив: код => строковое имя. |
| `jsonSerialize()` | `array` | Возвращает `['timestamp', 'code', 'text', 'office']`. |

#### Справочник кодов status

| Константа | Код | Описание |
|---|---|---|
| `UNREGISTERED` | `-1` | Отправление не зарегистрировано или удалено в системе перевозчика. |
| `CREATED` | `1` | Накладная создана в плагине, но ещё не отправлена перевозчику. |
| `REGISTERED` | `50` | Зарегистрировано у перевозчика (присвоен трек-номер). |
| `ACCEPTED` | `100` | Принято на склад перевозчика / обработка на таможне. |
| `PACKED` | `150` | Отправление упаковано и готово к отправке. |
| `IN_TRANSIT` | `200` | В пути между городами или складами. |
| `ARRIVED` | `300` | Прибыло на склад города назначения или в пункт выдачи. |
| `ON_DELIVERY` | `400` | Передано курьеру для доставки. |
| `PENDING` | `450` | Попытка доставки не удалась (получатель отсутствует, перенос и т.д.). |
| `DELIVERED` | `500` | Успешно доставлено получателю. |
| `PAID` | `550` | Доставлено, наложенный платеж получен. |
| `RETURNED` | `600` | Возврат (получатель отказался, невозможно доставить). |
| `RETURNING_TO_SENDER` | `650` | Отправление в пути обратно к отправителю. |
| `DELIVERED_TO_SENDER` | `699` | Возвратное отправление доставлено отправителю. |
| `UNKNOWN` | `1000` | Status не удалось сопоставить ни с одним известным кодом. |

### `Waybill`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Waybill`

Иммутабельный value-объект, представляющий накладную. Сеттеры возвращают клонированный экземпляр (паттерн иммутабельности).

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(?Track $track = null, ?float $shippingCost = null, ?DeliveryTerms $deliveryTerms = null, ?DeliveryType $deliveryType = null, ?bool $cod = null)` | | Создает накладную. Бросает `NegativePriceException`, если стоимость доставки отрицательная. |
| `getTrack()` | `?Track` | Возвращает трек-номер. |
| `setTrack(?Track $track)` | `Waybill` | Возвращает новый `Waybill` с обновленным трек-номером. |
| `getShippingCost()` | `?float` | Стоимость доставки (в базовой единице валюты). |
| `setShippingCost(?float $shippingCost)` | `Waybill` | Возвращает новый `Waybill` с обновленной стоимостью. Бросает `NegativePriceException` при отрицательном значении. |
| `getDeliveryTerms()` | `?DeliveryTerms` | Расчетный диапазон времени доставки. |
| `setDeliveryTerms(?DeliveryTerms $deliveryTerms)` | `Waybill` | Возвращает новый `Waybill` с обновленными сроками. |
| `getDeliveryType()` | `?DeliveryType` | Тип доставки. |
| `setDeliveryType(?DeliveryType $deliveryType)` | `Waybill` | Возвращает новый `Waybill` с обновленным типом. |
| `isCod()` | `?bool` | Включен ли наложенный платеж. |
| `setCod(?bool $cod)` | `Waybill` | Возвращает новый `Waybill` с обновленным флагом COD. |
| `jsonSerialize()` | `array` | Возвращает накладную в виде ассоциативного массива. |
| `createFromArray(array $data)` | `Waybill` | Статическая фабрика для восстановления `Waybill` из сериализованного массива. |

### `Track`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Waybill`

Value-объект для трек-номера.

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(string $track)` | | Валидация: 6-36 символов, допустимы только `A-Z`, `0-9`, `-`, `_`. Бросает `LogisticTrackException` при невалидном вводе. |
| `get()` | `string` | Возвращает трек-номер. |
| `__toString()` | `string` | Возвращает трек-номер как строку. |
| `jsonSerialize()` | `string` | Возвращает трек-номер. |

### `DeliveryTerms`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Waybill`

Value-объект для расчетного диапазона времени доставки в часах.

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(int $minHours, int $maxHours)` | | Валидация: `$minHours >= 0`, `$maxHours <= 8760` (1 год), `$minHours <= $maxHours`. Бросает `DeliveryTermsException`. |
| `getMinHours()` | `int` | Минимальное время доставки в часах. |
| `getMaxHours()` | `int` | Максимальное время доставки в часах. |
| `jsonSerialize()` | `array` | Возвращает `['minHours' => ..., 'maxHours' => ...]`. |

### `DeliveryType`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Waybill`

Enum value-объект для способа доставки. Наследует `EnumHelper`.

| Константа | Значение | Описание |
|---|---|---|
| `SELF_PICKUP` | `'SELF_PICKUP'` | Отправитель сам доставляет груз перевозчику. |
| `PICKUP_POINT` | `'PICKUP_POINT'` | Получатель забирает из пункта выдачи. |
| `COURIER` | `'COURIER'` | Курьерская доставка на адрес получателя. |

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(string $type)` | | Валидация по допустимым значениям. Бросает `OutOfEnumException`. |
| `get()` | `string` | Возвращает строку типа доставки. |
| `getAsString()` | `string` | Возвращает тип в читаемом виде. |
| `values()` | `array` | Возвращает `['SELF_PICKUP', 'PICKUP_POINT', 'COURIER']`. |
| `jsonSerialize()` | `string` | Возвращает строку типа доставки. |

### `LogisticOffice`

**Namespace:** `SalesRender\Plugin\Components\Logistic`

Представляет офис перевозчика или пункт выдачи с адресом, телефонами и часами работы.

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(?Address $address, array $phones, ?OpeningHours $openingHours)` | | Валидация телефонов по паттерну `^\+?\d{9,16}$`. Бросает `LogisticOfficePhoneException`. |
| `getAddress()` | `?Address` | Возвращает адрес офиса. |
| `getPhones()` | `array` | Возвращает список телефонных номеров. |
| `getOpeningHours()` | `?OpeningHours` | Возвращает расписание работы. |
| `jsonSerialize()` | `array` | Возвращает `['address', 'phones', 'openingHours']`. |
| `createFromArray(?array $data)` | `?self` | Статическая фабрика для восстановления из массива. Возвращает `null`, если входные данные `null`. |

### `OpeningHours`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Components`

Обертка над `spatie/opening-hours` для валидации расписания работы офиса.

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(array $schedule)` | | Валидирует расписание через `Spatie\OpeningHours`. Бросает `OpeningHoursException`. |
| `getSchedule()` | `array` | Возвращает валидированный массив расписания. |
| `jsonSerialize()` | `array` | Возвращает расписание. |

### `ShippingAttachment`

**Namespace:** `SalesRender\Plugin\Components\Logistic\Components`

Представляет файловое вложение (этикетка, накладная и т.д.), связанное с отправлением.

| Метод | Возврат | Описание |
|---|---|---|
| `__construct(string $name, Uri $uri)` | | Валидация имени: 1-255 символов. Бросает `ShippingAttachmentException`. |
| `getName()` | `string` | Возвращает имя вложения. |
| `getUri()` | `Uri` | Возвращает URI вложения. |
| `createFromArray(array $data)` | `self` | Статическая фабрика из `['name' => ..., 'uri' => ...]`. |
| `jsonSerialize()` | `array` | Возвращает `['name' => ..., 'uri' => ...]`. |

## Исключения

| Исключение | Источник | Условие |
|---|---|---|
| `DeliveryTermsException` | `DeliveryTerms` | Невалидные min/max часы (отрицательные, превышают 8760, или min > max). |
| `LogisticDataTooBigException` | `Logistic` | Массив данных превышает 2048 байт в сериализованном виде. |
| `LogisticOfficePhoneException` | `LogisticOffice` | Телефонный номер не соответствует паттерну `^\+?\d{9,16}$`. |
| `LogisticStatusTooLongException` | `LogisticStatus` | Текст status превышает 250 символов. |
| `LogisticTrackException` | `Track` | Невалидный трек-номер (не 6-36 символов или содержит недопустимые символы). |
| `NegativePriceException` | `Waybill` | Отрицательная стоимость доставки. |
| `OpeningHoursException` | `OpeningHours` | Невалидный формат расписания (валидация Spatie). |
| `ShippingAttachmentException` | `ShippingAttachment` | Имя вложения пустое или превышает 255 символов. |

## Примеры использования

### Создание накладной с записью логистики

Из `plugin-logistic-example` (`WaybillHandler`):

```php
use SalesRender\Plugin\Components\Logistic\Logistic;
use SalesRender\Plugin\Components\Logistic\LogisticStatus;
use SalesRender\Plugin\Components\Logistic\Waybill\DeliveryTerms;
use SalesRender\Plugin\Components\Logistic\Waybill\DeliveryType;
use SalesRender\Plugin\Components\Logistic\Waybill\Track;
use SalesRender\Plugin\Components\Logistic\Waybill\Waybill;

// Создание трек-номера
$track = new Track($data->get('waybill.track'));

// Формирование сроков доставки (в часах)
$terms = new DeliveryTerms(24, 72); // 1-3 дня

// Создание накладной
$waybill = new Waybill(
    $track,
    $price,                                       // стоимость доставки
    $terms,                                       // сроки доставки
    new DeliveryType(DeliveryType::PICKUP_POINT), // тип доставки
    true                                          // наложенный платеж
);

// Формирование агрегата логистики
$logistic = new Logistic(
    $waybill,
    new LogisticStatus(LogisticStatus::CREATED, 'Накладная создана')
);
```

### Маппинг status перевозчика на коды LogisticStatus

Из `plugin-logistic-cdek` (`TrackingHelper`):

```php
use SalesRender\Plugin\Components\Logistic\LogisticStatus;

// Маппинг status API СДЭК на коды LogisticStatus
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

// Создание status из данных трекинга
$status = new LogisticStatus(
    self::STATUS_CODES[$apiStatusCode],
    mb_substr($statusText, 0, 255),
    (new DateTime($statusDate))->getTimestamp(),
    $logisticOffice
);
```

### Создание LogisticOffice с часами работы

Из `plugin-logistic-cdek` (`TrackingHelper`):

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
    new Address('', 'Москва', 'ул. Примерная, 123'),
    ['+79001234567'],
    $openingHours
);
```

### Простое создание накладной (Новая Почта)

Из `plugin-logistic-novaposhta` (`WaybillHandler`):

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
    new LogisticStatus(LogisticStatus::CREATED, 'Накладная создана'),
    $deliveryData // произвольные метаданные (максимум 2 КБ)
);
```

### Отслеживание и обновление status (Новая Почта)

Из `plugin-logistic-novaposhta` (`TrackingCommand`):

```php
use SalesRender\Plugin\Components\Logistic\LogisticStatus;

// Таблица маппинга status
const TRACKING_STATUSES_TABLE = [
    1   => ['code' => LogisticStatus::REGISTERED, 'text' => 'Відправник створив накладну'],
    4   => ['code' => LogisticStatus::IN_TRANSIT, 'text' => 'Відправлення прямує до міста'],
    7   => ['code' => LogisticStatus::ARRIVED,    'text' => 'Прибув на відділення'],
    9   => ['code' => LogisticStatus::DELIVERED,  'text' => 'Відправлення отримано'],
    11  => ['code' => LogisticStatus::PAID,       'text' => 'Доставлено, грошовий переказ видано'],
    103 => ['code' => LogisticStatus::RETURNED,   'text' => 'Одержувач відмовився від відправлення'],
];

// Создание status и добавление к треку
$status = new LogisticStatus(
    $statusMapping['code'],
    $statusDescription,
    $statusTimestamp
);

$track->addStatus($status);
$track->save();
```

### Пакетная обработка отправлений со случайными status (пример)

Из `plugin-logistic-example` (`BatchShippingHandler`):

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

## Зависимости

| Пакет | Назначение |
|---|---|
| `salesrender/component-address` | Value-объекты `Address` и `Location` |
| `xakepehok/enum-helper` | Базовый класс для enum-типов (`LogisticStatus`, `DeliveryType`) |
| `xakepehok/value-object-builder` | Фабрика для построения value-объектов из массивов |
| `spatie/opening-hours` | Валидация расписания работы |
| `php-dto/uri` | Value-объект URI для вложений |

## Смотрите также

- [salesrender/plugin-core-logistic](https://github.com/SalesRender/plugin-core-logistic) -- инфраструктура ядра логистических плагинов (модель Track, WaybillHandler, BatchShippingHandler)
- [salesrender/component-address](https://github.com/SalesRender/component-address) -- value-объекты Address и Location
- [salesrender/plugin-component-special-request](https://github.com/SalesRender/plugin-component-special-request) -- используется Track для отправки уведомлений о status
- [salesrender/plugin-component-batch](https://github.com/SalesRender/plugin-component-batch) -- инфраструктура пакетной обработки, используемая обработчиками отправлений
