<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;

use Leadvertex\Components\Address\Address;
use Leadvertex\Plugin\Components\Logistic\Components\OpeningHours;
use PHPUnit\Framework\TestCase;
use Spatie\OpeningHours\Day;

class LogisticOfficeTest extends TestCase
{

    private Address $address;

    private array $phones;

    private OpeningHours $openingHours;

    private LogisticOffice $office;

    private LogisticOffice $officeNulls;

    protected function setUp(): void
    {
        parent::setUp();
        $this->address = new Address('', '', '');
        $this->phones = [
            '88002000600',
            '+78002000600',
        ];
        $this->openingHours = new OpeningHours([
            Day::MONDAY     => ['09:00-12:00', '13:00-18:00'],
            Day::TUESDAY    => ['09:00-12:00', '13:00-18:00'],
            Day::WEDNESDAY  => ['09:00-12:00'],
            Day::THURSDAY   => ['09:00-12:00', '13:00-18:00'],
            Day::FRIDAY     => ['09:00-12:00', '13:00-20:00'],
            Day::SATURDAY   => ['09:00-12:00', '13:00-16:00'],
            Day::SUNDAY     => [],
        ]);

        $this->office = new LogisticOffice(
            $this->address,
            $this->phones,
            $this->openingHours
        );

        $this->officeNulls = new LogisticOffice(null, [], null);
    }

    public function testGetAddress(): void
    {
        $this->assertSame($this->address, $this->office->getAddress());
        $this->assertNull($this->officeNulls->getAddress());
    }

    public function testGetPhones(): void
    {
        $this->assertSame($this->phones, $this->office->getPhones());
        $this->assertEmpty($this->officeNulls->getPhones());
    }

    public function testGetOpeningHours(): void
    {
        $this->assertSame($this->openingHours, $this->office->getOpeningHours());
        $this->assertNull($this->officeNulls->getOpeningHours());
    }

    public function testJsonSerialize(): void
    {
        $this->assertEquals(
            '{"address":{"postcode":"","region":"","city":"","address_1":"","address_2":""},"phones":["88002000600","+78002000600"],"openingHours":{"monday":["09:00-12:00","13:00-18:00"],"tuesday":["09:00-12:00","13:00-18:00"],"wednesday":["09:00-12:00"],"thursday":["09:00-12:00","13:00-18:00"],"friday":["09:00-12:00","13:00-20:00"],"saturday":["09:00-12:00","13:00-16:00"],"sunday":[]}}',
            json_encode($this->office)
        );
    }

}
