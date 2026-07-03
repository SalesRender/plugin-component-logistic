<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic\Waybill;

use SalesRender\Plugin\Components\Logistic\Exceptions\LogisticTrackException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TrackTest extends TestCase
{

    /**
     * @param string $number
     * @throws LogisticTrackException
     */
    #[DataProvider('validTrackDataProvider')]
    public function testConstructValidTrack(string $number): void
    {
        $track = new Track($number);
        $this->assertSame(trim($number), $track->get());
        $this->assertSame(trim($number), (string) $track);
    }

    /**
     * @param string $track
     */
    #[DataProvider('invalidTrackDataProvider')]
    public function testConstructInvalidTrack(string $track): void
    {
        $this->expectException(LogisticTrackException::class);
        new Track($track);
    }

    public static function validTrackDataProvider(): array
    {
        return [
            ['123456'],
            [str_repeat('1', 36)],
            ['abc_ABC-123'],
            [' abc_ABC-123 '],
        ];
    }

    public static function invalidTrackDataProvider(): array
    {
        return [
            ['      '],
            ['русскиебуквы'],
            ['12345'],
            [' 12345 '],
            ['******'],
            [str_repeat('1', 37)],
        ];
    }

}
