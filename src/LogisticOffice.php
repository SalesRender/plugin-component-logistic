<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


use JsonSerializable;
use Leadvertex\Components\Address\Address;
use Leadvertex\Components\Address\Location;
use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticOfficePhoneException;
use Leadvertex\Plugin\Components\Logistic\Components\OpeningHours;
use XAKEPEHOK\ValueObjectBuilder\VOB;

class LogisticOffice implements JsonSerializable
{

    protected ?Address $address;
    protected array $phones;
    protected ?OpeningHours $openingHours;

    /**
     * LogisticOffice constructor.
     * @param Address|null $address
     * @param array $phones
     * @param OpeningHours|null $openingHours
     * @throws LogisticOfficePhoneException
     */
    public function __construct(?Address $address, array $phones, ?OpeningHours $openingHours)
    {
        $this->address = $address;

        foreach ($phones as $phone) {
            if (!preg_match('~^\+?\d{9,16}$~ui', $phone)) {
                throw new LogisticOfficePhoneException();
            }
        }
        $this->phones = array_values($phones);

        $this->openingHours = $openingHours;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function getPhones(): array
    {
        return $this->phones;
    }

    public function getOpeningHours(): ?OpeningHours
    {
        return $this->openingHours;
    }

    public function jsonSerialize(): array
    {
        return [
            'address' => $this->address,
            'phones' => $this->phones,
            'openingHours' => $this->openingHours,
        ];
    }

    /**
     * @param ?array $data
     * @return static
     * @throws LogisticOfficePhoneException
     */
    public static function createFromArray(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }
        $address = ($data['address'] === null)
            ? null
            : VOB::buildFromValues(Address::class, [
            $data['address']['region'],
            $data['address']['city'],
            $data['address']['address_1'],
            $data['address']['address_2'] ?? '',
            $data['address']['postcode'] ?? '',
            $data['address']['countryCode'] ?? null,
            VOB::buildFromValues(Location::class, [
                $data['address']['location']['latitude'] ?? null,
                $data['address']['location']['longitude'] ?? null,
            ]),
        ]);

        return new LogisticOffice(
            $address,
            $data['phones'] ?? [],
            VOB::build(OpeningHours::class, $data['openingHours'] ?? null),
        );
    }
}